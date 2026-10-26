@extends('layouts.app')
@section('title', 'Profile and settings')
@section('content')
<section class="section dashboard-section"><div class="container form-page-container">
    <div class="form-page-heading"><span class="eyebrow">Account settings</span><h1>Your profile</h1><p>Manage your contact details and account security.</p></div>
    <div class="panel editor-form">
        <form method="POST" action="{{ route('profile.update') }}" class="form-stack">@csrf @method('PUT')
            <div class="form-section-label">Personal information</div>
            <label class="field"><span>Current password <b>*</b></span><input type="password" name="current_password" autocomplete="current-password" required><small class="form-note">Required to save changes to your account.</small><small class="field-error">@error('current_password'){{ $message }}@enderror</small></label>
            <label class="field"><span>Full name</span><input name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required></label>
            <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required><small class="form-note">{{ $user->hasVerifiedEmail() ? 'Verified address' : 'Not verified — check your inbox for a verification link.' }}</small></label>
            <label class="field"><span>Contact number</span><input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel"></label>
            @if($user->workspace)
                <div class="form-section-label">SK council</div>
                <div class="profile-workspace"><b>{{ $user->workspace->council_name }}</b><span>{{ $user->workspace->barangay }}, {{ $user->workspace->municipality }}, {{ $user->workspace->province }}</span><small>Workspace status: {{ ucfirst($user->workspace->status) }} · Account status: {{ ucfirst($user->account_status) }}</small></div>
            @endif
            <div class="form-actions"><button class="button" type="submit">Save profile</button></div>
        </form>
    </div>
    <div class="panel editor-form password-settings"><div class="form-section-label">Change password</div><p class="form-note">Confirm your current password before choosing a new one.</p>
        <form method="POST" action="{{ route('profile.password') }}" class="form-stack">@csrf @method('PUT')
            <label class="field"><span>Current password</span><input type="password" name="current_password" autocomplete="current-password" required><small class="field-error">@error('current_password'){{ $message }}@enderror</small></label>
            <label class="field"><span>New password</span><input type="password" name="password" autocomplete="new-password" minlength="12" required><small class="field-error">@error('password'){{ $message }}@enderror</small></label>
            <label class="field"><span>Confirm new password</span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <div class="form-actions"><button class="button button-outline" type="submit">Update password</button></div>
        </form>
    </div>
</div></section>
@endsection
