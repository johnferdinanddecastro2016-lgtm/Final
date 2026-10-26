@extends('layouts.app')
@section('title', 'Choose a new password')
@section('content')
<section class="auth-section"><div class="auth-card"><span class="eyebrow">Account recovery</span><h1>Choose a new password</h1><p class="auth-intro">Use at least 12 characters you haven't used before.</p>
    <form method="POST" action="{{ route('password.update') }}" class="form-stack">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email', $request->email) }}" autocomplete="email" required><small class="field-error">@error('email'){{ $message }}@enderror</small></label>
        <label class="field"><span>New password</span><input type="password" name="password" autocomplete="new-password" minlength="12" required><small class="field-error">@error('password'){{ $message }}@enderror</small></label>
        <label class="field"><span>Confirm new password</span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="button button-full" type="submit">Reset password</button>
    </form>
</div><aside class="auth-aside"><span class="aside-mark">SK<span>.</span></span><span class="eyebrow eyebrow-light">Keep your account secure</span><h2>One strong password protects your work.</h2><p>Never share your password with anyone, including people claiming to be support.</p></aside></section>
@endsection
