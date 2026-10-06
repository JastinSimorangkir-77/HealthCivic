<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_an_appointment(): void
    {
        $this->post(route('appointments.store'), [])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_patient_can_submit_an_appointment_to_their_own_account(): void
    {
        $patient = User::factory()->create([
            'name' => 'Siti Aminah',
            'phone' => '081234567890',
        ]);

        $response = $this->actingAs($patient)->post(route('appointments.store'), [
            'patient_name' => 'Nama yang dimanipulasi',
            'phone' => '089999999999',
            'service_type' => 'appointment',
            'specialty' => 'penyakit-dalam',
            'visit_date' => today()->addDay()->toDateString(),
            'time_slot' => '08.00–10.00',
            'insurance' => 'umum',
            'privacy_consent' => '1',
            'user_id' => 999,
        ]);

        $response->assertRedirect(route('home'));

        $appointment = Appointment::query()->firstOrFail();
        $this->assertSame($patient->id, $appointment->user_id);
        $this->assertSame('Siti Aminah', $appointment->patient_name);
        $this->assertSame('081234567890', $appointment->phone);
        $this->assertSame('appointment', $appointment->service_type);
        $this->assertNotSame('Siti Aminah', DB::table('appointments')->value('patient_name'));

        $this->actingAs($patient)->get(route('home'))
            ->assertOk()
            ->assertSee($appointment->appointment_code);
    }

    public function test_bpjs_visit_requires_a_bpjs_number_and_does_not_flash_personal_data(): void
    {
        $patient = User::factory()->create();

        $this->actingAs($patient)->from(route('bpjs.create'))->post(route('appointments.store'), [
            'patient_name' => 'Nama yang tidak boleh disimpan ke sesi',
            'phone' => '081234567890',
            'service_type' => 'bpjs',
            'specialty' => 'umum',
            'visit_date' => today()->addDay()->toDateString(),
            'time_slot' => '08.00–10.00',
            'insurance' => 'bpjs',
            'notes' => 'Catatan kesehatan privat',
            'privacy_consent' => '1',
        ])->assertRedirect(route('bpjs.create'))
            ->assertSessionHasErrors(['bpjs_number'], null, 'appointment')
            ->assertSessionHas('_old_input', fn (array $input): bool => ! isset($input['patient_name'])
                && ! isset($input['phone'])
                && ! isset($input['bpjs_number'])
                && ! isset($input['notes']));

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_consultation_notes_are_saved_encrypted_for_the_patient(): void
    {
        $patient = User::factory()->create();
        $notes = 'Nyeri yang tidak membaik selama beberapa hari.';

        $this->actingAs($patient)->post(route('appointments.store'), [
            'service_type' => 'consultation',
            'specialty' => 'umum',
            'visit_date' => today()->addDay()->toDateString(),
            'time_slot' => '08.00–10.00',
            'insurance' => 'umum',
            'notes' => $notes,
            'privacy_consent' => '1',
        ])->assertRedirect(route('home'));

        $appointment = Appointment::query()->firstOrFail();
        $this->assertSame($notes, $appointment->notes);
        $this->assertNotSame($notes, DB::table('appointments')->value('notes'));
        $this->assertSame($patient->id, $appointment->user_id);
    }

    public function test_bpjs_service_cannot_be_submitted_with_a_different_payment_method(): void
    {
        $patient = User::factory()->create();

        $this->actingAs($patient)->from(route('bpjs.create'))->post(route('appointments.store'), [
            'service_type' => 'bpjs',
            'specialty' => 'umum',
            'visit_date' => today()->addDay()->toDateString(),
            'time_slot' => '08.00–10.00',
            'insurance' => 'umum',
            'bpjs_number' => '1234567890123',
            'privacy_consent' => '1',
        ])->assertRedirect(route('bpjs.create'))
            ->assertSessionHasErrors(['insurance'], null, 'appointment');

        $this->assertDatabaseCount('appointments', 0);
    }
}
