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
        $activity = collect()
            ->concat(Program::where('workspace_id', $workspaceId)->latest()->limit(4)->get()->map(fn (Program $program) => [
                'kind' => 'Program',
                'title' => $program->title,
                'detail' => 'Program created · '.ucfirst($program->status),
                'at' => $program->created_at,
                'url' => route('programs.edit', $program),
            ]))
            ->concat(Event::where('workspace_id', $workspaceId)->latest()->limit(4)->get()->map(fn (Event $event) => [
                'kind' => 'Event',
                'title' => $event->title,
                'detail' => 'Event added to the workspace calendar',
                'at' => $event->created_at,
                'url' => route('events.index', ['edit' => $event->id]),
            ]))
            ->concat(Announcement::where('workspace_id', $workspaceId)->latest()->limit(4)->get()->map(fn (Announcement $announcement) => [
                'kind' => 'Announcement',
                'title' => $announcement->title,
                'detail' => 'Announcement · '.ucfirst($announcement->status),
                'at' => $announcement->created_at,
                'url' => route('announcements.edit', $announcement),
            ]))
            ->concat(ProgramRegistration::where('workspace_id', $workspaceId)->with('program')->latest()->limit(4)->get()->map(fn (ProgramRegistration $registration) => [
                'kind' => 'Registration',
                'title' => 'New program registration',
                'detail' => $registration->program->title.' · '.ucfirst($registration->status),
                'at' => $registration->created_at,
                'url' => route('workspace.registrations.index'),
            ]))
            ->sortByDesc('at')
            ->take(6)
            ->values();

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
            'recentActivity' => $activity,
        ]);
    }
}
