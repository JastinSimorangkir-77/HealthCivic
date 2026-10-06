<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_render_separate_login_and_registration_pages(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Selamat datang kembali')
            ->assertSee(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Buat akun pasien')
            ->assertSee(route('login'));
    }

    public function test_patient_can_register_and_is_signed_in_with_encrypted_contact_data(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Siti Aminah',
            'email' => 'SITI@example.com',
            'phone' => '081234567890',
            'password' => 'KataSandiPasien2026',
            'password_confirmation' => 'KataSandiPasien2026',
            'role' => 'admin',
            'privacy_consent' => '1',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $patient = User::query()->firstOrFail();
        $this->assertSame('siti@example.com', $patient->email);
        $this->assertSame('081234567890', $patient->phone);
        $this->assertSame(UserRole::Patient, $patient->role);
        $this->assertNotSame('081234567890', DB::table('users')->value('phone'));
        $this->assertNotNull($patient->privacy_consent_at);
    }

    public function test_registration_rejects_weak_passwords_and_missing_privacy_consent(): void
    {
        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'phone' => '081234567890',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors(['password', 'privacy_consent']);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_patient_can_sign_in_and_sign_out(): void
    {
        $patient = User::factory()->create([
            'email' => 'patient@example.com',
            'password' => 'CorrectHorseBatteryStaple2026',
        ]);

        $this->post(route('login.store'), [
            'email' => 'PATIENT@example.com',
            'password' => 'CorrectHorseBatteryStaple2026',
            'remember' => '1',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($patient);

        $this->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Anda telah keluar dengan aman.');

        $this->assertGuest();
    }

    public function test_patient_is_returned_to_the_protected_page_they_requested(): void
    {
        $patient = User::factory()->create();
        $this->get(route('queue.create'))
            ->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'email' => $patient->email,
            'password' => 'password',
        ])->assertRedirect(route('queue.create'));
    }

    public function test_invalid_login_returns_a_generic_error(): void
    {
        User::factory()->create(['email' => 'patient@example.com']);

        $this->from(route('login'))->post(route('login.store'), [
            'email' => 'patient@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_authenticated_patient_is_redirected_away_from_auth_pages(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('home'));

        $this->get(route('register'))->assertRedirect(route('home'));
    }
}
