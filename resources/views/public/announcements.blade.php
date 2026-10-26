@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow">The latest from local councils</span><h1>Community announcements</h1><p>Important updates and opportunities, shared directly by your SK council.</p></div></section>
<section class="section section-white"><div class="container">
    @if($announcements->isNotEmpty())
        <div class="announcement-grid announcement-grid-large">@foreach($announcements as $announcement)
            <a class="announcement-card" href="{{ route('public.announcement', $announcement) }}">
                @if($announcement->image_path)<img class="announcement-image" src="{{ Storage::disk('public')->url($announcement->image_path) }}" alt="">@endif
                <span class="card-date">{{ $announcement->published_at->format('M j, Y') }} · {{ $announcement->workspace->barangay }}</span><h3>{{ $announcement->title }}</h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($announcement->body), 150) }}</p><span class="text-link">Read update <span aria-hidden="true">→</span></span>
            </a>
        @endforeach</div>
        <div class="pagination-wrap">{{ $announcements->links() }}</div>
    @else<div class="empty-state"><span class="empty-icon">✳</span><h3>No announcements yet</h3><p>Published community updates will show up here.</p></div>@endif
</div></section>
@endsection
