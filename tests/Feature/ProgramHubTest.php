<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProgramHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_and_only_show_published_programs(): void
    {
        $workspace = Workspace::create([
            'council_name' => 'SK Riverside',
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => 'Riverside',
            'status' => 'active',
        ]);
        $published = $this->program($workspace, ['status' => 'published', 'is_public' => true]);
        $draft = $this->program($workspace, ['status' => 'draft', 'is_public' => true, 'title' => 'Private draft']);

        $this->get('/')->assertOk()->assertSee('Make every youth program');
        $this->get('/programs')->assertOk()->assertSee($published->title)->assertDontSee($draft->title);
        $this->get(route('public.program', $published))->assertOk()->assertSee($published->title);
        $this->get(route('public.program', $draft))->assertNotFound();
        $this->get('/events')->assertOk();
        $this->get('/announcements')->assertOk();
    }

    public function test_council_signup_creates_pending_workspace_and_sends_verification(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'account_type' => 'council',
            'name' => 'Avery Santos',
            'email' => 'avery@example.test',
            'password' => 'A-strong-password-2026',
            'password_confirmation' => 'A-strong-password-2026',
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => 'Riverside',
            'sk_position' => 'Chairperson',
            'council_name' => 'SK Riverside',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('login'));
        $user = User::where('email', 'avery@example.test')->firstOrFail();
        $this->assertSame('workspace_owner', $user->role);
        $this->assertSame('pending', $user->account_status);
        $this->assertSame('pending', $user->workspace->status);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_participant_signup_does_not_create_a_council_workspace(): void
    {
        Notification::fake();

        $this->post('/register', [
            'account_type' => 'participant',
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.test',
            'password' => 'A-strong-password-2026',
            'password_confirmation' => 'A-strong-password-2026',
            'terms' => '1',
        ])->assertRedirect(route('login'));

        $user = User::where('email', 'jordan@example.test')->firstOrFail();
        $this->assertSame('participant', $user->role);
        $this->assertSame('active', $user->account_status);
        $this->assertNull($user->workspace_id);
        $this->assertDatabaseCount('workspaces', 0);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_logout_redirects_home_and_clears_the_authenticated_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_opening_logout_url_does_not_show_a_method_not_allowed_error(): void
    {
        $this->get('/logout')->assertRedirect(route('home'));
    }

    public function test_pending_council_accounts_cannot_create_programs(): void
    {
        $workspace = $this->workspace('Riverside');
        $workspace->update(['status' => 'pending']);
        $member = User::factory()->create([
            'workspace_id' => $workspace->id,
            'role' => 'workspace_owner',
            'account_status' => 'pending',
        ]);

        $this->actingAs($member)->get('/programs/create')->assertForbidden();
    }

    public function test_active_workspace_members_can_create_programs_and_dashboard_uses_database_totals(): void
    {
        $workspace = $this->workspace('Riverside');
        $member = User::factory()->create([
            'workspace_id' => $workspace->id,
            'role' => 'workspace_owner',
            'account_status' => 'active',
        ]);
        $this->actingAs($member);

        $this->get(route('dashboard'))->assertOk()->assertSee('Total programs');
        $this->get(route('events.index'))->assertOk()->assertSee('Workspace calendar');
        $this->get(route('announcements.index'))->assertOk()->assertSee('Council workspace');
        $this->post(route('programs.store'), [
            'title' => 'Youth Leadership Workshop',
            'description' => 'A practical workshop for youth leaders.',
            'category' => 'Education',
            'starts_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDays(10)->addHours(2)->format('Y-m-d H:i:s'),
            'status' => 'published',
            'is_public' => '1',
        ])->assertRedirect(route('programs.index'));

        $this->assertDatabaseHas('programs', [
            'workspace_id' => $workspace->id,
            'title' => 'Youth Leadership Workshop',
            'status' => 'published',
            'is_public' => 1,
        ]);
        $this->get(route('dashboard'))->assertOk()->assertSee('Youth Leadership Workshop');
    }

    public function test_workspace_lists_filter_events_and_announcements_without_leaking_other_tenants(): void
    {
        $workspace = $this->workspace('Riverside');
        $otherWorkspace = $this->workspace('Hilltop');
        $member = User::factory()->create([
            'workspace_id' => $workspace->id,
            'role' => 'workspace_owner',
            'account_status' => 'active',
        ]);
        Event::create([
            'workspace_id' => $workspace->id,
            'title' => 'Published youth forum',
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHour(),
            'status' => 'published',
            'is_public' => true,
        ]);
        Event::create([
            'workspace_id' => $workspace->id,
            'title' => 'Draft planning session',
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHour(),
            'status' => 'draft',
            'is_public' => false,
        ]);
        Event::create([
            'workspace_id' => $otherWorkspace->id,
            'title' => 'Another council private event',
            'starts_at' => now()->addDays(4),
            'ends_at' => now()->addDays(4)->addHour(),
            'status' => 'draft',
        ]);
        Announcement::create([
            'workspace_id' => $workspace->id,
            'title' => 'Published community update',
            'slug' => 'published-community-update',
            'body' => 'A useful update for residents.',
            'status' => 'published',
            'is_public' => true,
            'published_at' => now(),
        ]);
        Announcement::create([
            'workspace_id' => $workspace->id,
            'title' => 'Internal draft update',
            'slug' => 'internal-draft-update',
            'body' => 'This draft is not public.',
            'status' => 'draft',
            'is_public' => false,
        ]);
        Announcement::create([
            'workspace_id' => $otherWorkspace->id,
            'title' => 'Other council private update',
            'slug' => 'other-council-private-update',
            'body' => 'Another tenant draft.',
            'status' => 'draft',
            'is_public' => false,
        ]);

        $this->actingAs($member)
            ->get(route('events.index', ['status' => 'draft']))
            ->assertOk()
            ->assertSee('Draft planning session')
            ->assertDontSee('<h3>Published youth forum</h3>', false)
            ->assertDontSee('Another council private event');

        $this->get(route('announcements.index', ['status' => 'draft']))
            ->assertOk()
            ->assertSee('Internal draft update')
            ->assertDontSee('Published community update')
            ->assertDontSee('Other council private update');
    }

    public function test_same_barangay_name_in_different_municipalities_is_allowed(): void
    {
        foreach (['Santa Rosa', 'Biñan'] as $index => $municipality) {
            $response = $this->post('/register', [
                'account_type' => 'council',
                'name' => "Council User {$index}",
                'email' => "council{$index}@example.test",
                'password' => 'A-strong-password-2026',
                'password_confirmation' => 'A-strong-password-2026',
                'province' => 'Laguna',
                'municipality' => $municipality,
                'barangay' => 'Poblacion',
                'sk_position' => 'Chairperson',
                'council_name' => "SK Poblacion {$municipality}",
                'terms' => '1',
            ]);

            $response->assertRedirect(route('login'));
        }

        $this->assertDatabaseCount('workspaces', 2);
    }

    public function test_duplicate_council_identifiers_are_rejected_case_insensitively(): void
    {
        $this->workspace('Riverside');

        $response = $this->post('/register', [
            'account_type' => 'council',
            'name' => 'Another Council User',
            'email' => 'another@example.test',
            'password' => 'A-strong-password-2026',
            'password_confirmation' => 'A-strong-password-2026',
            'province' => ' LAGUNA ',
            'municipality' => 'SANTA ROSA',
            'barangay' => 'riverside',
            'sk_position' => 'Chairperson',
            'council_name' => 'SK Riverside',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors('barangay');
        $this->assertDatabaseCount('workspaces', 1);
    }

    public function test_cancelled_registrations_do_not_use_capacity(): void
    {
        $workspace = $this->workspace('Riverside');
        $program = $this->program($workspace, [
            'capacity' => 1,
            'slug' => 'limited-program',
        ]);
        ProgramRegistration::create([
            'program_id' => $program->id,
            'workspace_id' => $workspace->id,
            'name' => 'Prior Participant',
            'email' => 'prior@example.test',
            'status' => 'cancelled',
        ]);

        $this->post(route('public.program.register', $program), [
            'name' => 'New Participant',
            'email' => 'new@example.test',
        ])->assertRedirect(route('registration.confirmation'));
    }

    public function test_active_workspace_members_cannot_view_another_tenants_private_program(): void
    {
        $firstWorkspace = $this->workspace('Riverside');
        $secondWorkspace = $this->workspace('Hilltop');
        $member = User::factory()->create([
            'workspace_id' => $firstWorkspace->id,
            'role' => 'workspace_owner',
            'account_status' => 'active',
        ]);
        $foreignProgram = $this->program($secondWorkspace, ['status' => 'draft', 'is_public' => false]);

        $this->actingAs($member)->get(route('public.program', $foreignProgram))->assertForbidden();
    }

    public function test_public_program_registration_respects_capacity(): void
    {
        $workspace = $this->workspace('Riverside');
        $program = $this->program($workspace, [
            'status' => 'published',
            'is_public' => true,
            'capacity' => 1,
        ]);

        $this->post(route('public.program.register', $program), [
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.test',
        ])->assertRedirect(route('registration.confirmation'));

        $this->post(route('public.program.register', $program), [
            'name' => 'Casey Lee',
            'email' => 'casey@example.test',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('program_registrations', 1);
    }

    public function test_registration_confirmation_is_not_accessible_without_a_completed_registration(): void
    {
        $this->get(route('registration.confirmation'))->assertNotFound();
    }

    private function workspace(string $barangay): Workspace
    {
        return Workspace::create([
            'council_name' => "SK {$barangay}",
            'province' => 'Laguna',
            'municipality' => 'Santa Rosa',
            'barangay' => $barangay,
            'status' => 'active',
        ]);
    }

    private function program(Workspace $workspace, array $attributes = []): Program
    {
        return Program::create(array_merge([
            'workspace_id' => $workspace->id,
            'title' => 'Youth Leadership Workshop',
            'slug' => Str::slug($attributes['title'] ?? 'Youth Leadership Workshop'),
            'description' => 'A public workshop for local youth.',
            'category' => 'Education',
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(10)->addHours(3),
            'status' => 'published',
            'is_public' => true,
        ], $attributes));
    }
}
