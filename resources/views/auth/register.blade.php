@extends('layouts.app')
@section('title', 'Create an account')
@section('content')
<section class="auth-section auth-section-wide">
    <aside class="auth-aside register-aside"><span class="aside-mark">SK<span>.</span></span><span class="eyebrow eyebrow-light">Start with what matters</span><h2>Better organized.<br>More connected.</h2><p>Build a stronger link between your local council and the young people you serve.</p><ul class="aside-list"><li>✓ One home for programs and events</li><li>✓ Clear updates for your community</li><li>✓ Registration that keeps participation simple</li></ul><div class="aside-bottom">Your account is protected with secure sign-in.</div></aside>
    <div class="auth-card register-card">
        <span class="eyebrow">Join SK ProgramHub</span><h1>Create your account</h1><p class="auth-intro">Choose how you’ll use ProgramHub to get started.</p>
        <form method="POST" action="{{ route('register') }}" class="form-stack" data-loading-form data-register-form>
            @csrf
            <fieldset class="account-choice"><legend>I'm joining as</legend><label><input type="radio" name="account_type" value="participant" @checked(old('account_type', 'participant') === 'participant') data-account-type><span>Youth participant</span></label><label><input type="radio" name="account_type" value="council" @checked(old('account_type') === 'council') data-account-type><span>SK council</span></label></fieldset>
            <label class="field"><span>Full name <b>*</b></span><input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required><small class="field-error">@error('name'){{ $message }}@enderror</small></label>
            <label class="field"><span>Official email address <b>*</b></span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required><small class="field-error">@error('email'){{ $message }}@enderror</small></label>
            <label class="field"><span>Contact number <small>(optional)</small></span><input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"><small class="field-error">@error('phone'){{ $message }}@enderror</small></label>
            <div class="council-fields" data-council-fields @if(old('account_type') !== 'council') hidden @endif>
                <div class="form-section-label">Your SK council</div>
                <label class="field"><span>Council or organization name <b>*</b></span><input type="text" name="council_name" value="{{ old('council_name') }}" data-required-council><small class="field-error">@error('council_name'){{ $message }}@enderror</small></label>
                <div class="field-row">
                    <label class="field"><span>Province <b>*</b></span><input type="text" name="province" value="{{ old('province') }}" data-required-council><small class="field-error">@error('province'){{ $message }}@enderror</small></label>
                    <label class="field"><span>City / municipality <b>*</b></span><input type="text" name="municipality" value="{{ old('municipality') }}" data-required-council><small class="field-error">@error('municipality'){{ $message }}@enderror</small></label>
                </div>
                <div class="field-row">
                    <label class="field"><span>Barangay <b>*</b></span><input type="text" name="barangay" value="{{ old('barangay') }}" data-required-council><small class="field-error">@error('barangay'){{ $message }}@enderror</small></label>
                    <label class="field"><span>SK position <b>*</b></span><input type="text" name="sk_position" value="{{ old('sk_position') }}" data-required-council><small class="field-error">@error('sk_position'){{ $message }}@enderror</small></label>
                </div>
                <p class="form-note">Council access stays pending until an authorized reviewer verifies your request.</p>
            </div>
            <label class="field"><span>Password <b>*</b></span><span class="password-wrap"><input type="password" name="password" autocomplete="new-password" minlength="12" required data-strength-input><button type="button" class="password-toggle" data-password-toggle aria-label="Show password">Show</button></span><span class="strength-meter"><i data-strength-meter></i></span><small class="strength-label" data-strength-label>Use at least 12 characters.</small><small class="field-error">@error('password'){{ $message }}@enderror</small></label>
            <label class="field"><span>Confirm password <b>*</b></span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <label class="check-field terms-check"><input type="checkbox" name="terms" value="1" required @checked(old('terms'))><span>I agree to the <a href="{{ route('home') }}#terms">Terms of Use</a> and <a href="{{ route('home') }}#privacy">Privacy Policy</a>.</span></label>
            <small class="field-error">@error('terms'){{ $message }}@enderror</small>
            <button class="button button-full" type="submit"><span>Create account</span><span class="button-loading">Creating account…</span></button>
        </form>
        <p class="auth-switch">Already registered? <a href="{{ route('login') }}">Log in</a></p>
    </div>
</section>
@endsection
