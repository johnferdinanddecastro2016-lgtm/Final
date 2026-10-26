@extends('layouts.app')
@section('title', $event->title)
@section('content')
<section class="page-hero"><div class="container"><a class="back-link" href="{{ route('public.events') }}">← All events</a><span class="eyebrow">{{ $event->workspace->barangay }} · {{ $event->starts_at->format('F j, Y') }}</span><h1>{{ $event->title }}</h1><p>{{ $event->workspace->council_name }} · {{ $event->workspace->municipality }}</p></div></section>
<section class="section section-white"><article class="container article-content"><div class="detail-facts">
    <div><small>Starts</small><strong>{{ $event->starts_at->format('l, F j · g:i A') }}</strong></div><div><small>Ends</small><strong>{{ $event->ends_at->format('F j · g:i A') }}</strong></div><div><small>Venue</small><strong>{{ $event->venue ?: 'To be announced' }}</strong></div>
    @if($event->program)<div><small>Related program</small><strong><a href="{{ route('public.program', $event->program) }}">{{ $event->program->title }}</a></strong></div>@endif
    </div>@if($event->description)<h2>About this event</h2><div class="prose">{{ $event->description }}</div>@endif
    <p class="article-byline">Organized by {{ $event->workspace->council_name }} · {{ $event->workspace->barangay }}, {{ $event->workspace->municipality }}</p></article></section>
@endsection
