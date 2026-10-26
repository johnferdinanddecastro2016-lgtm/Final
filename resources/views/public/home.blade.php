@extends('layouts.app')

@section('title', 'Programs for every community')

@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow"><span class="status-dot"></span> A better way to bring youth programs to life</span>
            <h1>Make every youth program <span>count.</span></h1>
            <p>One clear place for Sangguniang Kabataan councils to organize programs, share local updates, and bring young people together.</p>
            <div class="hero-actions">
                <a class="button" href="{{ route('register') }}">Get started <span aria-hidden="true">→</span></a>
                <a class="button button-outline" href="{{ route('public.programs') }}">Explore programs</a>
            </div>
            <div class="hero-proof"><div class="avatar-stack"><span>Y</span><span>C</span><span>+</span></div><span>Built for local leaders and the communities they serve</span></div>
        </div>
        <div class="hero-art" aria-label="Illustration of a community event calendar" role="img">
            <div class="art-sun"></div><div class="art-hill art-hill-one"></div><div class="art-hill art-hill-two"></div>
            <div class="calendar-card">
                <div class="calendar-top"><span>COMMUNITY CALENDAR</span><span class="calendar-menu">•••</span></div>
                <div class="calendar-date"><strong>{{ now()->format('F') }}</strong><span>{{ now()->format('Y') }}</span></div>
                <div class="calendar-days"><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span></div>
                <div class="calendar-grid">
                    @foreach (range(1, 28) as $day)
                        <span class="{{ $day === now()->day ? 'today' : '' }}">{{ $day }}</span>
                    @endforeach
                </div>
                <div class="calendar-event"><span class="event-mark"></span><span><b>Community in action</b><small>Programs, made easy to find</small></span></div>
            </div>
            <div class="art-note"><span class="note-icon">✓</span><span><b>Local ideas, real impact</b><small>Made for every barangay</small></span></div>
            <div class="art-person person-one"></div><div class="art-person person-two"></div>
        </div>
    </div>
</section>

<section class="trust-strip"><div class="container trust-inner"><span>DESIGNED FOR</span><strong>SK Councils</strong><i></i><strong>Youth Leaders</strong><i></i><strong>Local Communities</strong></div></section>

<section class="section section-white" id="programs">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow">Discover what's happening</span><h2>Programs that move us forward</h2><p>Find opportunities to learn, connect, and make a difference close to home.</p></div><a class="text-link" href="{{ route('public.programs') }}">View all programs <span aria-hidden="true">→</span></a></div>
        @if ($programs->isNotEmpty())
            <div class="card-grid">
                @foreach ($programs as $program)
                    <article class="program-card">
                        <div class="program-visual visual-{{ $loop->iteration }}">
                            @if ($program->image_path)<img src="{{ Storage::disk('public')->url($program->image_path) }}" alt="">@else<span class="visual-letter">{{ strtoupper(substr($program->category, 0, 1)) }}</span>@endif
                            <span class="category-pill">{{ $program->category }}</span>
                        </div>
                        <div class="program-card-body"><span class="card-date">{{ $program->starts_at->format('D, M j · g:i A') }}</span><h3><a href="{{ route('public.program', $program) }}">{{ $program->title }}</a></h3><p>{{ \Illuminate\Support\Str::limit($program->description, 112) }}</p><div class="card-meta"><span>⌖ {{ $program->venue ?: $program->workspace->barangay }}</span><a href="{{ route('public.program', $program) }}" aria-label="View {{ $program->title }}">↗</a></div></div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state"><span class="empty-icon">✳</span><h3>Good things are taking shape</h3><p>Public programs will appear here once local councils publish them.</p><a class="text-link" href="{{ route('register') }}">Register an SK council <span aria-hidden="true">→</span></a></div>
        @endif
    </div>
</section>

<section class="section" id="events">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow">Save the date</span><h2>Coming up in your community</h2><p>Make room for new experiences, new skills, and new connections.</p></div><a class="text-link" href="{{ route('public.events') }}">See all events <span aria-hidden="true">→</span></a></div>
        @if ($events->isNotEmpty())
            <div class="event-list">
                @foreach ($events as $event)
                    <article class="event-row"><div class="event-date"><strong>{{ $event->starts_at->format('d') }}</strong><span>{{ $event->starts_at->format('M') }}</span></div><div class="event-info"><span class="eyebrow">{{ $event->starts_at->format('l · g:i A') }}</span><h3>{{ $event->title }}</h3><p>{{ $event->venue ?: $event->workspace->barangay }} · {{ $event->workspace->municipality }}</p></div><span class="event-arrow" aria-hidden="true">↗</span></article>
                @endforeach
            </div>
        @else
            <div class="empty-inline">Upcoming community events will be listed here.</div>
        @endif
    </div>
</section>

<section class="section section-white" id="about">
    <div class="container benefit-grid">
        <div><span class="eyebrow">One hub, more momentum</span><h2>Less time coordinating.<br><span>More time making change.</span></h2><p class="lead">Give your council a shared home for the plans, updates, and opportunities that make a community thrive.</p><a class="button" href="{{ route('register') }}">Bring your council on board <span aria-hidden="true">→</span></a></div>
        <div class="benefit-list">
            <article><span class="benefit-number">01</span><div><h3>Organize with clarity</h3><p>Keep programs, schedules, and registrations together in one workspace for your council.</p></div></article>
            <article><span class="benefit-number">02</span><div><h3>Reach more young people</h3><p>Publish opportunities and announcements where your whole community can find them.</p></div></article>
            <article><span class="benefit-number">03</span><div><h3>Build trust through action</h3><p>Share what's planned and help young people take part in shaping local priorities.</p></div></article>
        </div>
    </div>
</section>

<section class="section section-white how-section"><div class="container">
    <div class="section-heading"><div><span class="eyebrow">Getting started is simple</span><h2>From an idea to a community in action</h2><p>A clear path for councils and young people to plan, find, and join local programs.</p></div></div>
    <div class="how-grid"><article><span>01</span><h3>Set up your council</h3><p>Create a workspace and verify your official council details.</p></article><article><span>02</span><h3>Share your program</h3><p>Publish schedules and announcements for the community to discover.</p></article><article><span>03</span><h3>Bring people together</h3><p>Accept registrations and turn participation into local impact.</p></article></div>
</div></section>

<section class="section section-compact" id="announcements">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow">From the community</span><h2>Latest announcements</h2></div><a class="text-link" href="{{ route('public.announcements') }}">All announcements <span aria-hidden="true">→</span></a></div>
        @if ($announcements->isNotEmpty())
            <div class="announcement-grid">@foreach ($announcements as $announcement)<a class="announcement-card" href="{{ route('public.announcement', $announcement) }}"><span class="card-date">{{ $announcement->published_at->format('M j, Y') }} · {{ $announcement->workspace->barangay }}</span><h3>{{ $announcement->title }}</h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($announcement->body), 130) }}</p><span class="text-link">Read update <span aria-hidden="true">→</span></span></a>@endforeach</div>
        @else
            <div class="empty-inline">Official public announcements from local councils will appear here.</div>
        @endif
    </div>
</section>

<section class="legal-section" id="privacy"><div class="container"><span class="eyebrow">Privacy</span><h2>Your information stays purposeful.</h2><p>ProgramHub uses account and registration details to secure workspaces, verify council access, and help organizers respond to program registrations. Public pages show only content a council has explicitly published. Avoid submitting sensitive personal information in public forms.</p></div></section>
<section class="legal-section legal-section-muted" id="terms"><div class="container"><span class="eyebrow">Terms of use</span><h2>Use the hub responsibly.</h2><p>Use accurate information, respect participant privacy, and publish only content you are authorized to share. Council access is subject to verification and may be suspended if workspace information or activity is not authorized.</p></div></section>

<section class="cta-band" id="contact"><div class="container cta-inner"><div><span class="eyebrow eyebrow-light">Your community's next chapter starts here</span><h2>Have a program to share?</h2><p>Set up your council workspace and bring your plans to the people they’re for.</p></div><a class="button button-light" href="{{ route('register') }}">Create your workspace <span aria-hidden="true">→</span></a></div></section>
@endsection
