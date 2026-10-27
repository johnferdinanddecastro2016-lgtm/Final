@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<section class="section dashboard-section"><div class="container dashboard-container">
    <div class="dashboard-heading"><div><span class="eyebrow">Workspace overview</span><h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ \Illuminate\Support\Str::before($user->name, ' ') }}.</h1><p>{{ $user->workspace->council_name }} · {{ $user->workspace->barangay }}, {{ $user->workspace->municipality }}</p></div><a class="button" href="{{ route('programs.create') }}">＋ Create a program</a></div>
    <div class="workspace-layout">
    <aside class="workspace-sidebar" aria-label="Workspace navigation">
        <span class="sidebar-title">WORKSPACE</span>
        <a class="sidebar-link active" href="{{ route('dashboard') }}"><span>▦</span>Overview</a>
        <a class="sidebar-link" href="{{ route('public.programs') }}"><span>▣</span>Programs</a>
        <a class="sidebar-link" href="{{ route('events.index') }}"><span>◷</span>Events</a>
        <a class="sidebar-link" href="{{ route('workspace.registrations.index') }}"><span>♙</span>Registrations</a>
        <a class="sidebar-link" href="{{ route('announcements.index') }}"><span>↗</span>Announcements</a>
        <a class="sidebar-link" href="{{ route('profile.edit') }}"><span>○</span>Profile & settings</a>
        <div class="sidebar-tenant"><small>YOUR COUNCIL</small><b>{{ $user->workspace->barangay }}</b><span>{{ $user->workspace->municipality }}</span></div>
    </aside>
    <div class="workspace-main">
    <div class="dashboard-nav mobile-dashboard-nav"><a class="active" href="{{ route('dashboard') }}">Overview</a><a href="{{ route('public.programs') }}">Programs</a><a href="{{ route('events.index') }}">Events</a><a href="{{ route('workspace.registrations.index') }}">Registrations</a><a href="{{ route('announcements.index') }}">Announcements</a><a href="{{ route('profile.edit') }}">Profile</a></div>
    <div class="stats-grid">
        <article class="stat-card"><span class="stat-label">Total programs</span><span class="stat-icon stat-blue">▣</span><strong>{{ $programCount }}</strong><small>Across all program statuses</small></article>
        <article class="stat-card"><span class="stat-label">Upcoming events</span><span class="stat-icon stat-teal">◷</span><strong>{{ $upcomingEventCount }}</strong><small>Next events scheduled</small></article>
        <article class="stat-card"><span class="stat-label">Published announcements</span><span class="stat-icon stat-violet">↗</span><strong>{{ $publishedAnnouncementCount }}</strong><small>Shared with the community</small></article>
        <article class="stat-card"><span class="stat-label">Registrations</span><span class="stat-icon stat-amber">♙</span><strong>{{ $registrationCount }}</strong><small>{{ $pendingRegistrationCount }} awaiting review</small></article>
    </div>
    <div class="dashboard-grid">
        <section class="panel dashboard-programs"><div class="panel-heading"><div><span class="eyebrow">On the horizon</span><h2>Upcoming programs</h2></div><a class="text-link" href="{{ route('programs.create') }}">Add program <span aria-hidden="true">→</span></a></div>
            @forelse($upcomingPrograms as $program)
                <a class="compact-row" href="{{ route('public.program', $program) }}"><span class="compact-date"><b>{{ $program->starts_at->format('d') }}</b><small>{{ $program->starts_at->format('M') }}</small></span><span class="compact-copy"><b>{{ $program->title }}</b><small>{{ $program->category }} · {{ $program->starts_at->format('M j, g:i A') }}</small></span><span class="status-pill status-{{ $program->status }}">{{ ucfirst($program->status) }}</span></a>
            @empty<div class="panel-empty"><span>◷</span><b>Your calendar is clear</b><small>Create a program to get started.</small><a class="text-link" href="{{ route('programs.create') }}">Create a program →</a></div>@endforelse
        </section>
        <section class="panel chart-panel"><div class="panel-heading"><div><span class="eyebrow">Your programs</span><h2>Status at a glance</h2></div></div><div class="chart-wrap"><canvas id="program-status-chart" data-values="{{ $programStatusCounts->toJson() }}" aria-label="Program counts by status" role="img"></canvas></div><p class="chart-note">Counts update from programs in your authorized workspace.</p></section>
        <section class="panel"><div class="panel-heading"><div><span class="eyebrow">Coming up</span><h2>Events</h2></div><a class="text-link" href="{{ route('events.index') }}">Manage <span aria-hidden="true">→</span></a></div>
            @forelse($upcomingEvents as $event)<div class="mini-event"><span class="mini-event-date">{{ $event->starts_at->format('M j') }}</span><div><b>{{ $event->title }}</b><small>{{ $event->starts_at->format('g:i A') }} · {{ $event->venue ?: 'Venue not set' }}</small></div></div>@empty<div class="panel-empty"><small>No events scheduled yet.</small><a class="text-link" href="{{ route('events.index') }}">Add an event →</a></div>@endforelse
        </section>
        <section class="panel"><div class="panel-heading"><div><span class="eyebrow">Recently updated</span><h2>Announcements</h2></div><a class="text-link" href="{{ route('announcements.index') }}">Manage <span aria-hidden="true">→</span></a></div>
            @forelse($recentAnnouncements as $announcement)<a class="announcement-compact" href="{{ route('announcements.edit', $announcement) }}"><b>{{ $announcement->title }}</b><small>{{ $announcement->updated_at->diffForHumans() }} · {{ ucfirst($announcement->status) }}</small></a>@empty<div class="panel-empty"><small>No announcements yet.</small><a class="text-link" href="{{ route('announcements.create') }}">Write an update →</a></div>@endforelse
        </section>
    </div>
    <section class="quick-actions"><div><span class="eyebrow">Keep the momentum going</span><h2>Quick actions</h2></div><a href="{{ route('programs.create') }}"><span>＋</span><b>Create a program</b><small>Plan your next initiative</small></a><a href="{{ route('events.index') }}"><span>◷</span><b>Add an event</b><small>Put it on the calendar</small></a><a href="{{ route('announcements.create') }}"><span>↗</span><b>Publish an update</b><small>Keep everyone informed</small></a></section>
    <section class="panel activity-panel">
        <div class="panel-heading"><div><span class="eyebrow">Your workspace</span><h2>Recent activity</h2></div></div>
        @forelse($recentActivity as $activity)
            <a class="activity-row" href="{{ $activity['url'] }}">
                <span class="activity-marker activity-{{ strtolower($activity['kind']) }}"></span>
                <span class="activity-copy"><b>{{ $activity['title'] }}</b><small>{{ $activity['detail'] }}</small></span>
                <span class="activity-time">{{ $activity['at']->diffForHumans() }}</span>
            </a>
        @empty
            <div class="panel-empty"><span>✦</span><b>Your activity will show up here</b><small>Create a program, schedule an event, or share a council update.</small></div>
        @endforelse
    </section>
    </div>
    </div>
</div></section>
@endsection
