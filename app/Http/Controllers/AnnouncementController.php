<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        if (! $this->canManage($request)) {
            $announcements = Announcement::query()->where('status', 'published')->where('is_public', true)
                ->whereNotNull('published_at')->where('published_at', '<=', now())
                ->with('workspace')->latest('published_at')->paginate(10)->withQueryString();

            return view('public.announcements', compact('announcements'));
        }
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:180'],
            'status' => ['nullable', 'in:draft,published'],
        ]);
        $workspaceAnnouncements = Announcement::where('workspace_id', $request->user()->workspace_id);
        $announcements = (clone $workspaceAnnouncements)
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()->paginate(10)->withQueryString();
        $statusCounts = (clone $workspaceAnnouncements)->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');
        $publicCount = (clone $workspaceAnnouncements)->where('status', 'published')->where('is_public', true)
            ->whereNotNull('published_at')->where('published_at', '<=', now())->count();

        return view('announcements.index', compact('announcements', 'statusCounts', 'publicCount'));
    }

    public function create(Request $request)
    {
        abort_unless($this->canManage($request), 403);

        return view('announcements.form', ['announcement' => new Announcement]);
    }

    public function store(Request $request)
    {
        abort_unless($this->canManage($request), 403);
        $data = $this->validateAnnouncement($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        $data['is_public'] = $request->boolean('is_public') && $data['status'] === 'published';
        $data['published_at'] = $data['is_public'] ? ($data['published_at'] ?? now()) : null;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        unset($data['image']);
        $request->user()->workspace->announcements()->create($data);

        return redirect()->route('announcements.index')->with('status', 'Announcement created.');
    }

    public function edit(Request $request, Announcement $announcement)
    {
        abort_unless($this->canManage($request) && $announcement->workspace_id === $request->user()->workspace_id, 404);

        return view('announcements.form', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        abort_unless($this->canManage($request) && $announcement->workspace_id === $request->user()->workspace_id, 404);
        $data = $this->validateAnnouncement($request);
        $data['is_public'] = $request->boolean('is_public') && $data['status'] === 'published';
        $data['published_at'] = $data['is_public'] ? ($data['published_at'] ?? $announcement->published_at ?? now()) : null;

        if ($request->hasFile('image')) {
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $data['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        unset($data['image']);
        $announcement->update($data);

        return redirect()->route('announcements.index')->with('status', 'Announcement updated.');
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        abort_unless($this->canManage($request) && $announcement->workspace_id === $request->user()->workspace_id, 404);

        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }
        $announcement->delete();

        return back()->with('status', 'Announcement deleted.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:20000'],
            'status' => ['required', 'in:draft,published'],
            'is_public' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    private function canManage(Request $request): bool
    {
        $user = $request->user();

        return $user !== null
            && $user->isWorkspaceMember()
            && $user->hasVerifiedEmail()
            && $user->account_status === 'active'
            && $user->workspace?->status === 'active';
    }
}
