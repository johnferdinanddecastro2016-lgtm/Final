@extends('layouts.app')
@section('title', $announcement->exists ? 'Edit announcement' : 'Write an announcement')
@section('content')
<section class="section dashboard-section"><div class="container form-page-container">
    <a class="back-link" href="{{ route('announcements.index') }}">← Back to announcements</a><div class="form-page-heading"><span class="eyebrow">Community update</span><h1>{{ $announcement->exists ? 'Edit announcement' : 'Write an announcement' }}</h1><p>Keep it useful, clear, and easy for your community to act on.</p></div>
    <form method="POST" enctype="multipart/form-data" action="{{ $announcement->exists ? route('announcements.update', $announcement) : route('announcements.store') }}" class="panel editor-form">@csrf @if($announcement->exists) @method('PUT') @endif
        <label class="field"><span>Title <b>*</b></span><input name="title" value="{{ old('title', $announcement->title) }}" maxlength="180" required></label>
        <label class="field"><span>Announcement <b>*</b></span><textarea name="body" rows="10" maxlength="20000" required>{{ old('body', $announcement->body) }}</textarea><small class="form-note">Plain text only; formatting is kept safe for public display.</small></label>
        <div class="field-row"><label class="field"><span>Status</span><select name="status"><option value="draft" @selected(old('status', $announcement->status ?: 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $announcement->status) === 'published')>Published</option></select></label><label class="field"><span>Publish date</span><input type="datetime-local" name="published_at" value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}"></label></div>
        <label class="field"><span>Featured image</span><input type="file" name="image" accept="image/*" data-image-input><small class="form-note">JPG, PNG, or WebP up to 5 MB.</small><img class="image-preview" data-image-preview alt="Image preview" @if($announcement->image_path) src="{{ Storage::disk('public')->url($announcement->image_path) }}" @else hidden @endif></label>
        <label class="check-field"><input type="checkbox" name="is_public" value="1" @checked(old('is_public', $announcement->is_public))><span>Publish this announcement publicly</span></label>
        <div class="form-actions"><a class="button button-outline" href="{{ route('announcements.index') }}">Cancel</a><button class="button" type="submit">{{ $announcement->exists ? 'Save changes' : 'Create announcement' }}</button></div>
    </form>
</div></section>
@endsection
