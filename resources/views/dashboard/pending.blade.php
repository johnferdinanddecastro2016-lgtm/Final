@extends('layouts.app')
@section('title', 'Workspace verification')
@section('content')
<section class="section"><div class="container narrow-container">
    <div class="pending-card"><span class="pending-icon">◷</span><span class="eyebrow">Workspace review in progress</span><h1>Thanks, {{ $user->name }}.</h1><p>Your email is {{ $user->hasVerifiedEmail() ? 'verified' : 'waiting for verification' }}, and your SK council workspace is pending authorization.</p>
        <ol class="review-steps"><li class="{{ $user->hasVerifiedEmail() ? 'step-done' : '' }}"><span>1</span><div><b>Confirm your email</b><small>Verify the link sent to {{ $user->email }}.</small></div></li><li><span>2</span><div><b>Council authorization</b><small>An authorized reviewer must approve your council details before management access is enabled.</small></div></li></ol>
        @unless($user->hasVerifiedEmail())<form method="POST" action="{{ route('verification.send') }}">@csrf<button class="button" type="submit">Send verification email</button></form>@endunless
        <p class="form-note">If you believe this is taking too long, contact your platform administrator.</p>
    </div>
</div></section>
@endsection
