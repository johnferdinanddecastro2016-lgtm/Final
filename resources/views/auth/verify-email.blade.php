@extends('layouts.app')
@section('title', 'Verify your email')
@section('content')
<section class="auth-section"><div class="auth-card"><span class="eyebrow">One last step</span><h1>Verify your email</h1><p class="auth-intro">Use the link we sent to <strong>{{ auth()->user()->email }}</strong> to confirm this address. Check your spam folder if it isn't in your inbox.</p>
    @if (session('status'))<div class="inline-alert">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('verification.send') }}">@csrf<button class="button button-full" type="submit">Send a new verification link</button></form>
</div><aside class="auth-aside"><span class="aside-mark">SK<span>.</span></span><span class="eyebrow eyebrow-light">Your community is waiting</span><h2>Confirm your email to continue.</h2><p>Account security helps keep every workspace and opportunity trustworthy.</p></aside></section>
@endsection
