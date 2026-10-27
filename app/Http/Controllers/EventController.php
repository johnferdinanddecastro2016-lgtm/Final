<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($this->canManage($request), 403);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:160'],
            'edit' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:draft,published'],
        ]);
        $workspaceEvents = Event::where('workspace_id', $request->user()->workspace_id);
        $events = (clone $workspaceEvents)
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('starts_at')->paginate(15)->withQueryString();
        $calendarEvents = (clone $workspaceEvents)
            ->get()->map(fn (Event $event) => [
                'title' => $event->title,
                'start' => $event->starts_at->toIso8601String(),
                'end' => $event->ends_at->toIso8601String(),
                'url' => route('events.index', ['edit' => $event->id]),
            ])->values();
        $statusCounts = (clone $workspaceEvents)->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');
        $upcomingCount = (clone $workspaceEvents)->where('starts_at', '>=', now())->count();
        $editingEvent = isset($filters['edit'])
            ? Event::where('workspace_id', $request->user()->workspace_id)->find($filters['edit'])
            : null;

        return view('events.index', compact('events', 'calendarEvents', 'editingEvent', 'statusCounts', 'upcomingCount'));
    }

    public function store(Request $request)
    {
        abort_unless($this->canManage($request), 403);
        $workspaceId = $request->user()->workspace_id;
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'venue' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', Rule::exists('programs', 'id')->where('workspace_id', $workspaceId)],
            'status' => ['required', 'in:draft,published'],
            'is_public' => ['nullable', 'boolean'],
        ]);
        $data['is_public'] = $request->boolean('is_public') && $data['status'] === 'published';

        $request->user()->workspace->events()->create($data);

        return back()->with('status', 'Event added.');
    }

    public function destroy(Request $request, Event $event)
    {
        abort_unless($this->canManage($request) && $event->workspace_id === $request->user()->workspace_id, 404);
        $event->delete();

        return back()->with('status', 'Event deleted.');
    }

    public function update(Request $request, Event $event)
    {
        abort_unless($this->canManage($request) && $event->workspace_id === $request->user()->workspace_id, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'venue' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', Rule::exists('programs', 'id')->where('workspace_id', $request->user()->workspace_id)],
            'status' => ['required', 'in:draft,published'],
            'is_public' => ['nullable', 'boolean'],
        ]);
        $data['is_public'] = $request->boolean('is_public') && $data['status'] === 'published';
        $event->update($data);

        return redirect()->route('events.index')->with('status', 'Event updated.');
    }

    private function canManage(Request $request): bool
    {
        $user = $request->user();

        return $user->isWorkspaceMember()
            && $user->hasVerifiedEmail()
            && $user->account_status === 'active'
            && $user->workspace?->status === 'active';
    }
}
