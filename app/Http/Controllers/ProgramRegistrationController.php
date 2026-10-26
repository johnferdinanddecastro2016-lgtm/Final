<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProgramRegistrationController extends Controller
{
    public function store(Request $request, Program $program)
    {
        abort_unless(
            $program->status === 'published'
                && $program->is_public
                && $program->starts_at->isFuture(),
            404
        );

        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => mb_strtolower(trim($email))]);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('program_registrations', 'email')->where('program_id', $program->id),
            ],
        ]);
        $data['email'] = mb_strtolower($data['email']);

        if ($request->user()?->role === 'participant') {
            if ($data['email'] !== mb_strtolower($request->user()->email)) {
                throw ValidationException::withMessages(['email' => 'Use the email address verified on your participant account.']);
            }
            $data['name'] = $request->user()->name;
        }

        $registration = DB::transaction(function () use ($program, $request, $data) {
            $lockedProgram = Program::whereKey($program->id)->lockForUpdate()->firstOrFail();

            if (! ($lockedProgram->status === 'published'
                && $lockedProgram->is_public
                && $lockedProgram->starts_at->isFuture()
                && (! $lockedProgram->registration_deadline || $lockedProgram->registration_deadline->isFuture()))) {
                throw ValidationException::withMessages(['email' => 'Registration for this program has closed.']);
            }

            if ($lockedProgram->capacity !== null
                && $lockedProgram->activeRegistrations()->count() >= $lockedProgram->capacity) {
                throw ValidationException::withMessages(['email' => 'This program has reached its participant capacity.']);
            }

            if ($lockedProgram->registrations()->where('email', $data['email'])->exists()) {
                throw ValidationException::withMessages(['email' => 'This email is already registered for the program.']);
            }

            return ProgramRegistration::create([
                ...$data,
                'program_id' => $lockedProgram->id,
                'workspace_id' => $lockedProgram->workspace_id,
                'user_id' => $request->user()?->role === 'participant' ? $request->user()->id : null,
                'status' => 'pending',
            ]);
        });

        return redirect()->route('registration.confirmation')->with('registration', $registration->id);
    }

    public function confirmation()
    {
        abort_unless(session()->has('registration'), 404);

        return view('public.registration-confirmation');
    }

    public function mine(Request $request)
    {
        return view('registrations.mine', [
            'registrations' => $request->user()->registrations()->with('program')->latest()->paginate(10),
        ]);
    }

    public function cancel(Request $request, ProgramRegistration $registration)
    {
        abort_unless($registration->user_id === $request->user()->id, 404);
        abort_unless($registration->status === 'pending' && $registration->program->starts_at->isFuture(), 403);

        $registration->update(['status' => 'cancelled']);

        return back()->with('status', 'Registration cancelled.');
    }

    public function workspaceIndex(Request $request)
    {
        abort_unless($this->canManage($request), 403);

        return view('registrations.workspace', [
            'registrations' => ProgramRegistration::where('workspace_id', $request->user()->workspace_id)
                ->with('program')->latest()->paginate(15),
        ]);
    }

    public function updateStatus(Request $request, ProgramRegistration $registration)
    {
        abort_unless($this->canManage($request) && $registration->workspace_id === $request->user()->workspace_id, 404);
        $data = $request->validate(['status' => ['required', 'in:approved,rejected']]);
        $registration->update($data);

        return back()->with('status', 'Registration status updated.');
    }

    private function canManage(Request $request): bool
    {
        $user = $request->user();

        return $user->isWorkspaceMember()
            && $user->hasVerifiedEmail()
            && $user->account_status === 'active'
            && $user->workspace?->status === 'active';
    }
}
