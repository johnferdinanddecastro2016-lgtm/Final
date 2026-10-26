@extends('layouts.app')
@section('title', 'Community events')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow">Mark your calendar</span><h1>Community events</h1><p>Find the next opportunity to show up, connect, and get involved.</p></div></section>
<section class="section section-white"><div class="container">
    <div class="calendar-panel"><div class="panel-heading"><div><span class="eyebrow">Browse dates</span><h2>Events calendar</h2></div></div><div id="community-calendar" data-events="{{ $calendarEvents->toJson() }}"></div></div>
    @if($events->isNotEmpty())
        <div class="event-list">@foreach($events as $event)
            <a class="event-row event-row-link" href="{{ route('public.event', $event) }}"><div class="event-date"><strong>{{ $event->starts_at->format('d') }}</strong><span>{{ $event->starts_at->format('M') }}</span></div><div class="event-info"><span class="eyebrow">{{ $event->starts_at->format('l · g:i A') }}</span><h3>{{ $event->title }}</h3><p>{{ $event->venue ?: 'Venue to be announced' }} · {{ $event->workspace->barangay }}, {{ $event->workspace->municipality }}</p>@if($event->description)<p>{{ \Illuminate\Support\Str::limit($event->description, 180) }}</p>@endif</div><span class="event-arrow" aria-hidden="true">↗</span></a>
        @endforeach</div>
        <div class="pagination-wrap">{{ $events->links() }}</div>
    @else<div class="empty-state"><span class="empty-icon">◷</span><h3>No upcoming events yet</h3><p>Check back soon for what's happening in your community.</p></div>@endif
</div></section>
@endsection
