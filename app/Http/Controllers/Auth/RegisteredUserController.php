<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $workspace = null;

            if ($validated['account_type'] === 'council') {
                $workspace = Workspace::create([
                    'council_name' => $validated['council_name'],
                    'province' => $validated['province'],
                    'municipality' => $validated['municipality'],
                    'barangay' => $validated['barangay'],
                    'status' => 'pending',
                ]);
            }

            return User::create([
                'workspace_id' => $workspace?->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'sk_position' => $validated['sk_position'] ?? null,
                'role' => $validated['account_type'] === 'council' ? 'workspace_owner' : 'participant',
                'account_status' => $validated['account_type'] === 'council' ? 'pending' : 'active',
            ]);
        });

        event(new Registered($user));

        return Redirect::route('login')->with('status', $user->isWorkspaceMember()
            ? 'Your request has been received. Verify your email and wait for council authorization before managing a workspace.'
            : 'Your account has been created. Verify your email to continue.');
    }
}
