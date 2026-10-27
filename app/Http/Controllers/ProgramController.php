<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Models\Program;
use App\Models\ProgramRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user() || ! $request->user()->hasVerifiedEmail()
            || ! $request->user()->isWorkspaceMember()
            || $request->user()->account_status !== 'active'
            || $request->user()->workspace?->status !== 'active') {
            $filters = $request->validate([
                'search' => ['nullable', 'string', 'max:160'],
                'category' => ['nullable', 'string', 'max:100'],
            ]);
            $programs = Program::query()->where('status', 'published')->where('is_public', true)
                ->where('starts_at', '>=', now())->with('workspace')
                ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
                ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
                ->orderBy('starts_at')->paginate(9)->withQueryString();

            return view('public.programs', compact('programs'));
        }

        $this->authorize('create', Program::class);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:160'],
            'status' => ['nullable', 'in:draft,published,completed,archived'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);
        $programs = Program::query()->where('workspace_id', $request->user()->workspace_id)
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', '%'.$search.'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->withCount('registrations')->latest()->paginate(10)->withQueryString();

        $workspacePrograms = Program::where('workspace_id', $request->user()->workspace_id);
        $categories = (clone $workspacePrograms)
            ->distinct()->orderBy('category')->pluck('category');
        $statusCounts = (clone $workspacePrograms)->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');
        $registrationTotal = ProgramRegistration::where('workspace_id', $request->user()->workspace_id)->count();

        return view('programs.index', compact('programs', 'categories', 'statusCounts', 'registrationTotal'));
    }

    public function create()
    {
        $this->authorize('create', Program::class);

        return view('programs.form', ['program' => new Program]);
    }

    public function store(StoreProgramRequest $request)
    {
        $data = $request->validated();
        $data['workspace_id'] = $request->user()->workspace_id;
        $data['slug'] = $this->uniqueSlug($data['title'], $request->user()->workspace_id);
        $data['is_public'] = $request->boolean('is_public') && $data['status'] === 'published';

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('programs', 'public');
        }

        unset($data['image']);
        Program::create($data);

        return redirect()->route('programs.index')->with('status', 'Program created.');
    }

    public function show(Request $request, Program $program)
    {
        if (! $request->user()?->isWorkspaceMember()) {
            abort_unless($program->status === 'published' && $program->is_public, 404);
            $program->load('workspace');

            return view('public.program', compact('program'));
        }

        $this->authorize('view', $program);
        $program->loadCount('registrations');

        return view('programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $this->authorize('update', $program);

        return view('programs.form', compact('program'));
    }

    public function update(StoreProgramRequest $request, Program $program)
    {
        $this->authorize('update', $program);
        $data = $request->validated();
        $data['is_public'] = $request->boolean('is_public') && $data['status'] === 'published';

        if ($data['title'] !== $program->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $program->workspace_id, $program->id);
        }

        if ($request->hasFile('image')) {
            if ($program->image_path) {
                Storage::disk('public')->delete($program->image_path);
            }
            $data['image_path'] = $request->file('image')->store('programs', 'public');
        }

        unset($data['image']);
        $program->update($data);

        return redirect()->route('programs.show', $program)->with('status', 'Program updated.');
    }

    public function destroy(Program $program)
    {
        $this->authorize('delete', $program);

        if ($program->image_path) {
            Storage::disk('public')->delete($program->image_path);
        }
        $program->delete();

        return redirect()->route('programs.index')->with('status', 'Program deleted.');
    }

    private function uniqueSlug(string $title, int $workspaceId, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'program';
        $slug = $base;
        $suffix = 2;

        while (Program::where('workspace_id', $workspaceId)->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
