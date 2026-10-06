<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StaffConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_confirm_a_pending_appointment_and_the_action_is_audited(): void
    {
        $doctor = $this->createStaff(UserRole::Doctor);
        $patient = User::factory()->create();
        $appointment = $patient->appointments()->create(
            $this->appointmentAttributes('RS-261006-DOCTOR1', $patient),
        );

        $this->actingAs($doctor)
            ->from(route('admin.appointments.index'))
            ->post(route('admin.appointments.confirm', $appointment))
            ->assertRedirect(route('admin.appointments.index'))
            ->assertSessionHas('status');

        $appointment->refresh();
        $this->assertSame('dikonfirmasi', $appointment->status);
        $this->assertSame($doctor->id, $appointment->confirmed_by_user_id);
        $this->assertNotNull($appointment->confirmed_at);
    }

    public function test_pharmacy_admin_can_confirm_an_order_but_cannot_confirm_a_visit(): void
    {
        $pharmacyAdmin = $this->createStaff(UserRole::PharmacyAdmin);
        $patient = User::factory()->create();
        $order = $patient->pharmacyOrders()->create([
            'order_code' => 'FAR-261006-PHARM1',
            'patient_name' => $patient->name,
            'phone' => $patient->phone,
            'items' => [['sku' => 'bodrex', 'name' => 'Bodrex tablet', 'quantity' => 1, 'unit_price' => 5000]],
            'fulfillment' => 'pickup',
            'status' => 'menunggu_konfirmasi',
            'total' => 5000,
            'consent_accepted' => true,
        ]);
        $appointment = $patient->appointments()->create(
            $this->appointmentAttributes('RS-261006-PHARM1', $patient),
        );

        $this->actingAs($pharmacyAdmin)
            ->from(route('admin.pharmacy-orders.index'))
            ->post(route('admin.pharmacy-orders.confirm', $order))
            ->assertRedirect(route('admin.pharmacy-orders.index'))
            ->assertSessionHas('status');

        $order->refresh();
        $this->assertSame('dikonfirmasi', $order->status);
        $this->assertSame($pharmacyAdmin->id, $order->confirmed_by_user_id);
        $this->assertNotNull($order->confirmed_at);

        $this->get(route('admin.appointments.index'))->assertForbidden();
        $this->post(route('admin.appointments.confirm', $appointment))->assertForbidden();
        $this->assertSame('menunggu_konfirmasi', $appointment->fresh()->status);
    }

    public function test_doctor_cannot_view_or_confirm_pharmacy_orders(): void
    {
        $doctor = $this->createStaff(UserRole::Doctor);
        $patient = User::factory()->create();
        $order = $patient->pharmacyOrders()->create([
            'order_code' => 'FAR-261006-DOCTOR1',
            'patient_name' => $patient->name,
            'phone' => $patient->phone,
            'items' => [['sku' => 'bodrex', 'name' => 'Bodrex tablet', 'quantity' => 1, 'unit_price' => 5000]],
            'fulfillment' => 'pickup',
            'status' => 'menunggu_konfirmasi',
            'total' => 5000,
            'consent_accepted' => true,
        ]);

        $this->actingAs($doctor)->get(route('admin.pharmacy-orders.index'))->assertForbidden();
        $this->post(route('admin.pharmacy-orders.confirm', $order))->assertForbidden();
        $this->assertSame('menunggu_konfirmasi', $order->fresh()->status);
    }

    public function test_patients_cannot_access_staff_queues_and_staff_cannot_use_patient_services(): void
    {
        $patient = User::factory()->create();
        $doctor = $this->createStaff(UserRole::Doctor);

        $this->actingAs($patient)->get(route('admin.appointments.index'))->assertForbidden();
        $this->actingAs($patient)->get(route('admin.pharmacy-orders.index'))->assertForbidden();
        $this->actingAs($doctor)->get(route('appointments.create'))->assertForbidden();
        $this->get(route('pharmacy.index'))->assertForbidden();
    }

    public function test_admin_can_open_both_confirmation_queues(): void
    {
        $admin = $this->createStaff(UserRole::Admin);

        $this->actingAs($admin)->get(route('home'))
            ->assertRedirect(route('admin.appointments.index'));

        $this->actingAs($admin)->get(route('admin.appointments.index'))
            ->assertOk()
            ->assertSee('Konfirmasi kunjungan');

        $this->get(route('admin.pharmacy-orders.index'))
            ->assertOk()
            ->assertSee('Konfirmasi pesanan obat');
    }

    public function test_pharmacy_admin_lands_on_the_pharmacy_confirmation_queue(): void
    {
        $pharmacyAdmin = $this->createStaff(UserRole::PharmacyAdmin);

        $this->actingAs($pharmacyAdmin)->get(route('home'))
            ->assertRedirect(route('admin.pharmacy-orders.index'));
    }

    public function test_confirmation_cannot_be_applied_twice(): void
    {
        $doctor = $this->createStaff(UserRole::Doctor);
        $patient = User::factory()->create();
        $appointment = $patient->appointments()->create(
            $this->appointmentAttributes('RS-261006-ONCE1', $patient),
        );
        $appointment->update([
            'status' => 'dikonfirmasi',
            'confirmed_by_user_id' => $doctor->id,
            'confirmed_at' => now()->subMinute(),
        ]);
        $originalConfirmedAt = $appointment->confirmed_at;

        $this->actingAs($doctor)
            ->from(route('admin.appointments.index'))
            ->post(route('admin.appointments.confirm', $appointment))
            ->assertRedirect(route('admin.appointments.index'))
            ->assertSessionHas('error');

        $appointment->refresh();
        $this->assertSame($doctor->id, $appointment->confirmed_by_user_id);
        $this->assertTrue($originalConfirmedAt->equalTo($appointment->confirmed_at));
    }

    public function test_staff_confirmation_screens_show_patient_and_product_details(): void
    {
        $admin = $this->createStaff(UserRole::Admin);
        $patient = User::factory()->create([
            'name' => 'Siti Aminah',
            'phone' => '081234567890',
        ]);
        $patient->appointments()->create(
            $this->appointmentAttributes('RS-261006-VIEW01', $patient),
        );
        $patient->pharmacyOrders()->create([
            'order_code' => 'FAR-261006-VIEW01',
            'patient_name' => $patient->name,
            'phone' => $patient->phone,
            'items' => [['sku' => 'bodrex', 'name' => 'Bodrex tablet', 'quantity' => 1, 'unit_price' => 5000]],
            'fulfillment' => 'pickup',
            'status' => 'menunggu_konfirmasi',
            'total' => 5000,
            'consent_accepted' => true,
        ]);

        $this->actingAs($admin)->get(route('admin.appointments.index'))
            ->assertSee('RS-261006-VIEW01')
            ->assertSee('Siti Aminah');

        $this->get(route('admin.pharmacy-orders.index'))
            ->assertSee('FAR-261006-VIEW01')
            ->assertSee('Bodrex tablet');
    }

    private function createStaff(UserRole $role): User
    {
        $user = User::factory()->create();
        DB::table('users')->where('id', $user->id)->update(['role' => $role->value]);

        return $user->refresh();
    }

    /**
     * @return array<string, mixed>
     */
    private function appointmentAttributes(string $code, User $patient): array
    {
        return [
            'appointment_code' => $code,
            'patient_name' => $patient->name,
            'phone' => $patient->phone,
            'service_type' => 'appointment',
            'specialty' => 'umum',
            'visit_date' => today()->addDay(),
            'time_slot' => '08.00–10.00',
            'insurance' => 'umum',
            'status' => 'menunggu_konfirmasi',
            'consent_accepted' => true,
        ];
    }
}
