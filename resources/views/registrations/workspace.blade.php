@extends('layouts.app')
@section('title', 'Program registrations')
@section('content')
<section class="section dashboard-section"><div class="container dashboard-container">
    <div class="dashboard-heading"><div><span class="eyebrow">Council workspace</span><h1>Program registrations</h1><p>Review community interest in your programs.</p></div></div>
    <div class="dashboard-nav"><a href="{{ route('dashboard') }}">Overview</a><a href="{{ route('public.programs') }}">Programs</a><a class="active" href="{{ route('workspace.registrations.index') }}">Registrations</a></div>
    @if($registrations->isNotEmpty())<div class="table-wrap panel"><table class="data-table"><thead><tr><th>Participant</th><th>Program</th><th>Submitted</th><th>Status</th><th>Review</th></tr></thead><tbody>
        @foreach($registrations as $registration)<tr><td>{{ $registration->name }}<small>{{ $registration->email }}</small></td><td>{{ $registration->program->title }}</td><td>{{ $registration->created_at->format('M j, Y') }}</td><td><span class="status-pill status-{{ $registration->status }}">{{ ucfirst($registration->status) }}</span></td><td>@if($registration->status === 'pending')<div class="table-actions"><form method="POST" action="{{ route('workspace.registrations.update', $registration) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="approved"><button class="text-link" type="submit">Approve</button></form><form method="POST" action="{{ route('workspace.registrations.update', $registration) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><button class="danger-link" type="submit">Reject</button></form></div>@endif</td></tr>@endforeach
    </tbody></table></div><div class="pagination-wrap">{{ $registrations->links() }}</div>@else<div class="empty-state panel"><span class="empty-icon">♙</span><h3>No registrations yet</h3><p>Registrations for your published programs will appear here.</p></div>@endif
</div></section>
@endsection
