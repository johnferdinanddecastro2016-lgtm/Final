@extends('layouts.app')
@section('title', $program->title)
@section('content')
<section class="page-hero"><div class="container"><a class="back-link" href="{{ route('public.programs') }}">← All programs</a><span class="eyebrow">{{ $program->category }}</span><h1>{{ $program->title }}</h1><p>{{ $program->workspace->council_name }} · {{ $program->workspace->barangay }}, {{ $program->workspace->municipality }}</p></div></section>
<section class="section section-white"><div class="container detail-layout">
    <article class="detail-main">
        @if($program->image_path)<img class="detail-image" src="{{ Storage::disk('public')->url($program->image_path) }}" alt="Image for {{ $program->title }}">@else<div class="detail-illustration"><span>{{ strtoupper(substr($program->category, 0, 1)) }}</span></div>@endif
        <span class="eyebrow">About this program</span><div class="prose">{{ $program->description }}</div>
        @if($program->objectives)<h2>Program objectives</h2><div class="prose">{{ $program->objectives }}</div>@endif
        @if($program->requirements)<h2>Who can join</h2><div class="prose">{{ $program->requirements }}</div>@endif
    </article>
    <aside class="detail-sidebar">
        <div class="detail-facts"><span class="eyebrow">Program details</span>
            <div><small>Starts</small><strong>{{ $program->starts_at->format('l, F j · g:i A') }}</strong></div>
            <div><small>Ends</small><strong>{{ $program->ends_at->format('F j · g:i A') }}</strong></div>
            <div><small>Venue</small><strong>{{ $program->venue ?: 'To be announced' }}</strong></div>
            @if($program->registration_deadline)<div><small>Registration deadline</small><strong>{{ $program->registration_deadline->format('F j, Y') }}</strong></div>@endif
            @if($program->capacity)<div><small>Available places</small><strong>{{ max(0, $program->capacity - $program->activeRegistrations()->count()) }} of {{ $program->capacity }}</strong></div>@endif
        </div>
        @if($program->starts_at->isFuture() && (! $program->registration_deadline || $program->registration_deadline->isFuture()) && (! $program->capacity || $program->activeRegistrations()->count() < $program->capacity))
        <div class="registration-box"><h2>Interested? Join in.</h2><p>Leave your details and the council will follow up with next steps.</p>
            <form method="POST" action="{{ route('public.program.register', $program) }}" class="form-stack">@csrf
                <label class="field"><span>Your name</span><input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" autocomplete="name" required><small class="field-error">@error('name'){{ $message }}@enderror</small></label>
                <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" required><small class="field-error">@error('email'){{ $message }}@enderror</small></label>
                <button class="button button-full" type="submit">Register for this program</button>
            </form>
        </div>
        @else
        <div class="inline-alert">Registration for this program is closed.</div>
        @endif
    </aside>
</div></section>
@endsection
