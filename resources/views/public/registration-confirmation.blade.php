@extends('layouts.app')
@section('title', 'Registration received')
@section('content')
<section class="auth-section"><div class="auth-card confirmation-card"><span class="confirmation-icon">✓</span><span class="eyebrow">You're on the list</span><h1>Registration received</h1><p class="auth-intro">The SK council will review your registration and share updates using the email address you provided.</p><a class="button button-full" href="{{ route('public.programs') }}">Explore more programs</a></div></section>
@endsection
