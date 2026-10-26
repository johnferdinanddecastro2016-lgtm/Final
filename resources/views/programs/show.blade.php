@extends('layouts.app')
@section('title', $program->title)
@section('content')
<section class="section dashboard-section"><div class="container form-page-container">
    <a class="back-link" href="{{ route('public.programs') }}">← Back to programs</a><div class="dashboard-heading"><div><span class="eyebrow">Program details</span><h1>{{ $program->title }}</h1><p>{{ $program->category }} · {{ ucfirst($program->status) }}</p></div><a class="button button-outline" href="{{ route('programs.edit', $program) }}">Edit program</a></div>
    <div class="panel editor-form"><div class="detail-facts"><div><small>Schedule</small><strong>{{ $program->starts_at->format('M j, Y · g:i A') }} — {{ $program->ends_at->format('M j, Y · g:i A') }}</strong></div><div><small>Venue</small><strong>{{ $program->venue ?: 'Not set' }}</strong></div><div><small>Registrations</small><strong>{{ $program->registrations_count }}{{ $program->capacity ? ' / '.$program->capacity : '' }}</strong></div><div><small>Public visibility</small><strong>{{ $program->is_public ? 'Published publicly' : 'Workspace only' }}</strong></div></div><h2>Description</h2><div class="prose">{{ $program->description }}</div>@if($program->objectives)<h2>Objectives</h2><div class="prose">{{ $program->objectives }}</div>@endif</div>
</div></section>
@endsection
