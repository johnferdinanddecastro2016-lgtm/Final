@extends('layouts.app')
@section('title', 'Explore programs')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow">Opportunities near you</span><h1>Explore community programs</h1><p>Discover ways to learn, participate, and make an impact in your barangay.</p></div></section>
<section class="section section-white"><div class="container">
    <form method="GET" action="{{ route('public.programs') }}" class="filter-bar">
        <label class="search-field"><span class="sr-only">Search programs</span><input type="search" name="search" value="{{ request('search') }}" placeholder="Search programs…"></label>
        <label class="sr-only" for="category-filter">Filter by category</label><select id="category-filter" name="category"><option value="">All categories</option>@foreach ($programs->pluck('category')->unique()->sort() as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
        <button class="button button-small" type="submit">Search</button>
    </form>
    @if($programs->isNotEmpty())
        <div class="card-grid">@foreach ($programs as $program)
            <article class="program-card"><a class="program-visual visual-{{ ($loop->index % 3) + 1 }}" href="{{ route('public.program', $program) }}">
                @if($program->image_path)<img src="{{ Storage::disk('public')->url($program->image_path) }}" alt="">@else<span class="visual-letter">{{ strtoupper(substr($program->category, 0, 1)) }}</span>@endif
                <span class="category-pill">{{ $program->category }}</span></a>
                <div class="program-card-body"><span class="card-date">{{ $program->starts_at->format('D, M j · g:i A') }}</span><h3><a href="{{ route('public.program', $program) }}">{{ $program->title }}</a></h3><p>{{ \Illuminate\Support\Str::limit($program->description, 120) }}</p><div class="card-meta"><span>{{ $program->venue ?: $program->workspace->barangay }}</span><a href="{{ route('public.program', $program) }}">Details →</a></div></div>
            </article>
        @endforeach</div>
        <div class="pagination-wrap">{{ $programs->links() }}</div>
    @else
        <div class="empty-state"><span class="empty-icon">✳</span><h3>No programs found</h3><p>Try a different search, or check back when new opportunities are published.</p><a class="text-link" href="{{ route('public.programs') }}">Clear filters</a></div>
    @endif
</div></section>
@endsection
