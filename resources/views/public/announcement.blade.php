@extends('layouts.app')
@section('title', $announcement->title)
@section('content')
<section class="page-hero"><div class="container"><a class="back-link" href="{{ route('public.announcements') }}">← All announcements</a><span class="eyebrow">{{ $announcement->workspace->barangay }} · {{ $announcement->published_at->format('F j, Y') }}</span><h1>{{ $announcement->title }}</h1></div></section>
<section class="section section-white"><article class="container article-content">
    @if($announcement->image_path)<img class="detail-image" src="{{ Storage::disk('public')->url($announcement->image_path) }}" alt="">@endif
    <div class="prose announcement-prose">{!! nl2br(e($announcement->body)) !!}</div>
    <p class="article-byline">Shared by {{ $announcement->workspace->council_name }} · {{ $announcement->workspace->barangay }}, {{ $announcement->workspace->municipality }}</p>
    <a class="text-link" href="{{ route('public.announcements') }}">← Back to announcements</a>
</article></section>
@endsection
