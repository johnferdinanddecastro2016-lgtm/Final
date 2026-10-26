<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Program;
use App\Models\ProgramRegistration;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = request()->user()->load('workspace');

        if (! $user->isWorkspaceMember()) {
            return redirect()->route('home')->with('status', 'Your account is ready for community program registration.');
        }

        if (! $user->hasVerifiedEmail() || $user->account_status !== 'active' || $user->workspace?->status !== 'active') {
            return view('dashboard.pending', compact('user'));
        }

        $workspaceId = $user->workspace_id;
        $programs = Program::where('workspace_id', $workspaceId);

        return view('dashboard.index', [
            'user' => $user,
            'programCount' => (clone $programs)->count(),
            'upcomingEventCount' => Event::where('workspace_id', $workspaceId)->where('starts_at', '>=', now())->count(),
            'upcomingEvents' => Event::where('workspace_id', $workspaceId)
                ->where('starts_at', '>=', now())->orderBy('starts_at')->limit(5)->get(),
            'publishedAnnouncementCount' => Announcement::where('workspace_id', $workspaceId)
                ->where('status', 'published')->count(),
            'registrationCount' => ProgramRegistration::where('workspace_id', $workspaceId)->count(),
            'pendingRegistrationCount' => ProgramRegistration::where('workspace_id', $workspaceId)
                ->where('status', 'pending')->count(),
            'upcomingPrograms' => (clone $programs)->where('starts_at', '>=', now())
                ->orderBy('starts_at')->limit(5)->get(),
            'recentAnnouncements' => Announcement::where('workspace_id', $workspaceId)
                ->latest()->limit(4)->get(),
            'programStatusCounts' => (clone $programs)->selectRaw('status, count(*) as total')
                ->groupBy('status')->pluck('total', 'status'),
        ]);
    }
}
