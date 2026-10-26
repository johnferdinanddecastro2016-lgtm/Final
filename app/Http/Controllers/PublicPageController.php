<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Program;

class PublicPageController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'programs' => Program::query()
                ->where('status', 'published')->where('is_public', true)
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')->limit(3)->get(),
            'events' => Event::query()
                ->where('status', 'published')->where('is_public', true)
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')->limit(3)->get(),
            'announcements' => Announcement::query()
                ->where('status', 'published')->where('is_public', true)
                ->whereNotNull('published_at')->where('published_at', '<=', now())
                ->latest('published_at')->limit(3)->get(),
        ]);
    }

    public function programs()
    {
        $programs = Program::query()
            ->where('status', 'published')->where('is_public', true)
            ->where('starts_at', '>=', now())
            ->with('workspace')->orderBy('starts_at')->paginate(9);

        return view('public.programs', compact('programs'));
    }

    public function program(Program $program)
    {
        abort_unless($program->status === 'published' && $program->is_public, 404);
        $program->load('workspace');

        return view('public.program', compact('program'));
    }

    public function events()
    {
        $events = Event::query()
            ->where('status', 'published')->where('is_public', true)
            ->where('starts_at', '>=', now())
            ->with('workspace')->orderBy('starts_at')->paginate(12);

        $calendarEvents = Event::query()
            ->where('status', 'published')->where('is_public', true)
            ->with('workspace')->where('ends_at', '>=', now())
            ->orderBy('starts_at')->get()
            ->map(fn (Event $event) => [
                'title' => $event->title,
                'start' => $event->starts_at->toIso8601String(),
                'end' => $event->ends_at->toIso8601String(),
                'url' => route('public.event', $event),
            ])->values();

        return view('public.events', compact('events', 'calendarEvents'));
    }

    public function event(Event $event)
    {
        abort_unless($event->status === 'published' && $event->is_public, 404);
        $event->load(['workspace', 'program']);

        return view('public.event', compact('event'));
    }

    public function announcements()
    {
        $announcements = Announcement::query()
            ->where('status', 'published')->where('is_public', true)
            ->whereNotNull('published_at')->where('published_at', '<=', now())
            ->with('workspace')->latest('published_at')->paginate(10);

        return view('public.announcements', compact('announcements'));
    }

    public function announcement(Announcement $announcement)
    {
        abort_unless(
            $announcement->status === 'published'
                && $announcement->is_public
                && $announcement->published_at?->isPast(),
            404
        );
        $announcement->load('workspace');

        return view('public.announcement', compact('announcement'));
    }
}
