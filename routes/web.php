<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramRegistrationController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/programs', [ProgramController::class, 'index'])->name('public.programs');
Route::post('/programs/{program}/register', [ProgramRegistrationController::class, 'store'])->middleware('throttle:10,1')->name('public.program.register');
Route::get('/registration/confirmation', [ProgramRegistrationController::class, 'confirmation'])->name('registration.confirmation');
Route::get('/events', [PublicPageController::class, 'events'])->name('public.events');
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('public.announcements');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard')->with('status', 'Email verified.');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'A new verification link has been sent.');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::middleware('verified')->group(function () {
        Route::get('/my-registrations', [ProgramRegistrationController::class, 'mine'])->name('registrations.mine');
        Route::delete('/my-registrations/{registration}', [ProgramRegistrationController::class, 'cancel'])->name('registrations.cancel');
        Route::get('/programs/manage', [ProgramController::class, 'index'])->name('programs.index');
        Route::resource('programs', ProgramController::class)->except(['index', 'show', 'destroy']);
        Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');
        Route::get('/announcements/manage', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/workspace/registrations', [ProgramRegistrationController::class, 'workspaceIndex'])->name('workspace.registrations.index');
        Route::patch('/workspace/registrations/{registration}', [ProgramRegistrationController::class, 'updateStatus'])->name('workspace.registrations.update');
        Route::get('/events/manage', [EventController::class, 'index'])->name('events.index');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::resource('announcements', AnnouncementController::class)->except(['index', 'show']);
    });
});

Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('public.program');
Route::get('/events/{event}', [PublicPageController::class, 'event'])->name('public.event');
Route::get('/announcements/{announcement}', [PublicPageController::class, 'announcement'])->name('public.announcement');
