@extends('layouts.app')
@section('title', 'Reset your password')
@section('content')
<section class="auth-section"><div class="auth-card"><span class="eyebrow">Account recovery</span><h1>Forgot your password?</h1><p class="auth-intro">Enter the email linked to your account. If it matches, we’ll send a reset link.</p>
    @if (session('status'))<div class="inline-alert">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('password.email') }}" class="form-stack">@csrf
        <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus><small class="field-error">@error('email'){{ $message }}@enderror</small></label>
        <button class="button button-full" type="submit">Send reset link</button>
    </form><p class="auth-switch"><a href="{{ route('login') }}">← Back to log in</a></p>
</div><aside class="auth-aside"><span class="aside-mark">SK<span>.</span></span><span class="eyebrow eyebrow-light">We've got you</span><h2>A quick reset and you're back on track.</h2><p>For your protection, reset links expire after a short time.</p></aside></section>
@endsection
