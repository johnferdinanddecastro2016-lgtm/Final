@extends('layouts.app')
@section('title', 'Log in')
@section('content')
<section class="auth-section">
    <div class="auth-card">
        <span class="eyebrow">Welcome back</span><h1>Log in to your account</h1><p class="auth-intro">Pick up where your council left off.</p>
        <form method="POST" action="{{ route('login') }}" class="form-stack" data-loading-form>
            @csrf
            <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus><small class="field-error">@error('email'){{ $message }}@enderror</small></label>
            <label class="field"><span>Password</span><span class="password-wrap"><input type="password" name="password" autocomplete="current-password" required><button type="button" class="password-toggle" data-password-toggle aria-label="Show password">Show</button></span><small class="field-error">@error('password'){{ $message }}@enderror</small></label>
            <div class="form-inline"><label class="check-field"><input type="checkbox" name="remember" value="1"><span>Remember me</span></label><a href="{{ route('password.request') }}" class="text-link">Forgot password?</a></div>
            <button class="button button-full" type="submit"><span>Log in</span><span class="button-loading">Signing in…</span></button>
        </form>
        <p class="auth-switch">New to ProgramHub? <a href="{{ route('register') }}">Create an account</a></p>
    </div>
    <aside class="auth-aside"><span class="aside-mark">SK<span>.</span></span><span class="eyebrow eyebrow-light">Good work starts together</span><h2>One place to make local ideas happen.</h2><p>Bring your council's programs, schedules, and community updates together.</p><div class="aside-bottom">Trusted by leaders. Open to everyone.</div></aside>
</section>
@endsection
