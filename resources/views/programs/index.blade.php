@extends('layouts.app')
@section('title', 'Manage programs')
@section('content')
<section class="section dashboard-section"><div class="container dashboard-container">
    <div class="dashboard-heading"><div><span class="eyebrow">Council workspace</span><h1>Programs</h1><p>Plan, publish, and track your community initiatives.</p></div><a class="button" href="{{ route('programs.create') }}">＋ Create program</a></div>
    <div class="dashboard-nav"><a href="{{ route('dashboard') }}">Overview</a><a class="active" href="{{ route('public.programs') }}">Programs</a><a href="{{ route('events.index') }}">Events</a><a href="{{ route('workspace.registrations.index') }}">Registrations</a><a href="{{ route('announcements.index') }}">Announcements</a></div>
    <div class="list-overview">
        <a class="list-overview-card" href="{{ route('programs.index') }}"><span class="overview-icon overview-icon-blue">▣</span><span><small>All programs</small><strong>{{ $statusCounts->sum() }}</strong></span></a>
        <a class="list-overview-card" href="{{ route('programs.index', ['status' => 'published']) }}"><span class="overview-icon overview-icon-green">↗</span><span><small>Published</small><strong>{{ $statusCounts->get('published', 0) }}</strong></span></a>
        <a class="list-overview-card" href="{{ route('programs.index', ['status' => 'draft']) }}"><span class="overview-icon overview-icon-amber">◷</span><span><small>Drafts to finish</small><strong>{{ $statusCounts->get('draft', 0) }}</strong></span></a>
        <a class="list-overview-card" href="{{ route('workspace.registrations.index') }}"><span class="overview-icon overview-icon-violet">♙</span><span><small>Registrations</small><strong>{{ $registrationTotal }}</strong></span></a>
    </div>
    <form method="GET" action="{{ route('public.programs') }}" class="filter-bar panel"><label class="search-field"><span class="sr-only">Search programs</span><input type="search" name="search" value="{{ request('search') }}" placeholder="Search by title…"></label>
        <label class="sr-only" for="program-category">Category</label><select id="program-category" name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
        <label class="sr-only" for="program-status">Status</label><select id="program-status" name="status"><option value="">All statuses</option>@foreach(['draft','published','completed','archived'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select><button class="button button-small" type="submit">Filter</button>
        @if(request()->hasAny(['search', 'status', 'category']))<a class="text-link" href="{{ route('programs.index') }}">Clear filters</a>@endif
    </form>
    @if($programs->isNotEmpty())
        <div class="table-wrap panel"><table class="data-table"><thead><tr><th>Program</th><th>Schedule</th><th>Status</th><th>Visibility</th><th>Participants</th><th></th></tr></thead><tbody>
        @foreach($programs as $program)<tr><td><a class="table-title" href="{{ route('programs.show', $program) }}">{{ $program->title }}</a><small>{{ $program->category }}</small></td><td>{{ $program->starts_at->format('M j, Y') }}<small>{{ $program->starts_at->format('g:i A') }}</small></td><td><span class="status-pill status-{{ $program->status }}">{{ ucfirst($program->status) }}</span></td><td>{{ $program->is_public ? 'Public' : 'Workspace only' }}</td><td>{{ $program->registrations_count }}{{ $program->capacity ? ' / '.$program->capacity : '' }}</td><td class="table-actions"><a href="{{ route('programs.edit', $program) }}">Edit</a><form method="POST" action="{{ route('programs.destroy', $program) }}" data-confirm="Delete this program and its registrations?">@csrf @method('DELETE')<button type="submit" class="danger-link">Delete</button></form></td></tr>@endforeach
        </tbody></table></div><div class="pagination-wrap">{{ $programs->links() }}</div>
    @else<div class="empty-state panel"><span class="empty-icon">▣</span><h3>No programs here yet</h3><p>Start with a draft, then publish when your community is ready.</p><a class="button" href="{{ route('programs.create') }}">Create a program</a></div>@endif
</div></section>
@endsection
