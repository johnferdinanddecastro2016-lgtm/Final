@extends('layouts.app')
@section('title', 'Manage events')
@section('content')
<section class="section dashboard-section"><div class="container dashboard-container">
    <div class="dashboard-heading"><div><span class="eyebrow">Council workspace</span><h1>Events</h1><p>Schedule local gatherings and keep your community in the loop.</p></div><a href="#event-form" class="button">＋ Add event</a></div>
    <div class="dashboard-nav"><a href="{{ route('dashboard') }}">Overview</a><a href="{{ route('public.programs') }}">Programs</a><a class="active" href="{{ route('events.index') }}">Events</a><a href="{{ route('announcements.index') }}">Announcements</a></div>
    <div class="calendar-panel panel"><div class="panel-heading"><div><span class="eyebrow">Workspace calendar</span><h2>Schedule overview</h2></div></div><div id="workspace-calendar" data-events="{{ $calendarEvents->toJson() }}"></div></div>
    <div class="panel-heading listing-heading"><div><span class="eyebrow">All scheduled events</span><h2>Event list</h2></div></div>
    @if($events->isNotEmpty())<div class="event-list event-list-admin">@foreach($events as $event)
        <article class="event-row"><div class="event-date"><strong>{{ $event->starts_at->format('d') }}</strong><span>{{ $event->starts_at->format('M') }}</span></div><div class="event-info"><span class="eyebrow">{{ $event->starts_at->format('l · g:i A') }}</span><h3>{{ $event->title }}</h3><p>{{ $event->venue ?: 'Venue not set' }} · {{ ucfirst($event->status) }}{{ $event->is_public ? ' · Public' : '' }}</p></div><div class="table-actions"><a href="{{ route('events.index', ['edit' => $event->id]) }}#event-form">Edit</a><form method="POST" action="{{ route('events.destroy', $event) }}" data-confirm="Delete this event?">@csrf @method('DELETE')<button class="danger-link" type="submit">Delete</button></form></div></article>
    @endforeach</div><div class="pagination-wrap">{{ $events->links() }}</div>@else<div class="empty-state panel"><span class="empty-icon">◷</span><h3>No events scheduled</h3><p>Create the first event and add it to your community calendar.</p></div>@endif
    <div class="panel editor-form" id="event-form"><div class="panel-heading"><div><span class="eyebrow">{{ $editingEvent ? 'Update event details' : 'Plan something good' }}</span><h2>{{ $editingEvent ? 'Edit event' : 'Add an event' }}</h2></div></div>
        <form method="POST" action="{{ $editingEvent ? route('events.update', $editingEvent) : route('events.store') }}" class="form-stack">
            @csrf @if($editingEvent) @method('PUT') @endif
            <label class="field"><span>Event title <b>*</b></span><input name="title" value="{{ old('title', $editingEvent?->title) }}" required maxlength="160"></label>
            <label class="field"><span>Description</span><textarea name="description" rows="3" maxlength="5000">{{ old('description', $editingEvent?->description) }}</textarea></label>
            <div class="field-row"><label class="field"><span>Starts <b>*</b></span><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $editingEvent?->starts_at?->format('Y-m-d\TH:i')) }}" required></label><label class="field"><span>Ends <b>*</b></span><input type="datetime-local" name="ends_at" value="{{ old('ends_at', $editingEvent?->ends_at?->format('Y-m-d\TH:i')) }}" required></label></div>
            <div class="field-row"><label class="field"><span>Venue</span><input name="venue" value="{{ old('venue', $editingEvent?->venue) }}" maxlength="255"></label><label class="field"><span>Linked program</span><select name="program_id"><option value="">No linked program</option>@foreach(auth()->user()->workspace->programs()->orderBy('title')->get() as $program)<option value="{{ $program->id }}" @selected((string) old('program_id', $editingEvent?->program_id) === (string) $program->id)>{{ $program->title }}</option>@endforeach</select></label></div>
            <div class="field-row"><label class="field"><span>Status</span><select name="status"><option value="draft" @selected(old('status', $editingEvent?->status ?? 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $editingEvent?->status) === 'published')>Published</option></select></label><label class="check-field"><input type="checkbox" name="is_public" value="1" @checked(old('is_public', $editingEvent?->is_public))><span>Show on public calendar</span></label></div>
            <div class="form-actions">@if($editingEvent)<a class="button button-outline" href="{{ route('events.index') }}">Cancel edit</a>@endif<button class="button" type="submit">{{ $editingEvent ? 'Save event' : 'Add event' }}</button></div>
        </form>
    </div>
</div></section>
@endsection
