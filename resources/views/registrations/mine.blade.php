@extends('layouts.app')
@section('title', 'My registrations')
@section('content')
<section class="section dashboard-section"><div class="container dashboard-container">
    <div class="dashboard-heading"><div><span class="eyebrow">Your activity</span><h1>My registrations</h1><p>Keep track of the programs you signed up for.</p></div><a class="button button-outline" href="{{ route('public.programs') }}">Explore programs</a></div>
    @if($registrations->isNotEmpty())<div class="table-wrap panel"><table class="data-table"><thead><tr><th>Program</th><th>Registered</th><th>Status</th><th></th></tr></thead><tbody>
        @foreach($registrations as $registration)<tr><td><a class="table-title" href="{{ route('public.program', $registration->program) }}">{{ $registration->program->title }}</a><small>{{ $registration->program->starts_at->format('M j, Y · g:i A') }}</small></td><td>{{ $registration->created_at->format('M j, Y') }}</td><td><span class="status-pill status-{{ $registration->status }}">{{ ucfirst($registration->status) }}</span></td><td>@if($registration->status === 'pending' && $registration->program->starts_at->isFuture())<form method="POST" action="{{ route('registrations.cancel', $registration) }}" data-confirm="Cancel your registration?">@csrf @method('DELETE')<button class="danger-link">Cancel</button></form>@endif</td></tr>@endforeach
    </tbody></table></div><div class="pagination-wrap">{{ $registrations->links() }}</div>
    @else<div class="empty-state panel"><span class="empty-icon">♙</span><h3>No registrations yet</h3><p>When you join a public program, it will be listed here.</p><a class="button" href="{{ route('public.programs') }}">Find a program</a></div>@endif
</div></section>
@endsection
