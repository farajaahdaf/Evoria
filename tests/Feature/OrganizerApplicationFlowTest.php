<?php

namespace Tests\Feature;

use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizerApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendee_can_submit_organizer_application(): void
    {
        $user = User::factory()->create([
            'role' => 'attendee',
        ]);

        $response = $this->actingAs($user)->post(route('organizer.application.store'), [
            'company_name' => 'Komunitas Konser Nusantara',
            'description' => 'Fokus pada event musik dan komunitas.',
        ]);

        $response->assertRedirect(route('organizer.pending'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'organizer',
        ]);

        $this->assertDatabaseHas('organizer_profiles', [
            'user_id' => $user->id,
            'company_name' => 'Komunitas Konser Nusantara',
            'status' => 'pending',
        ]);
    }

    public function test_pending_organizer_is_redirected_to_pending_page_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'pending-organizer@example.com',
            'password' => 'password',
            'role' => 'organizer',
        ]);

        OrganizerProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Pending EO',
            'description' => 'Menunggu verifikasi',
            'status' => 'pending',
        ]);

        $response = $this->post('/login', [
            'email' => 'pending-organizer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/organizer/pending');
    }

    public function test_verified_organizer_is_redirected_away_from_application_page(): void
    {
        $organizer = User::factory()->create([
            'role' => 'organizer',
        ]);

        OrganizerProfile::create([
            'user_id' => $organizer->id,
            'company_name' => 'Verified EO',
            'status' => 'verified',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.application.create'));

        $response->assertRedirect(route('organizer.dashboard'));
    }
}
