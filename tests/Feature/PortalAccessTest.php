<?php

namespace Tests\Feature;

use App\Models\PharmacyOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PortalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_every_patient_service(): void
    {
        foreach ([
            route('home'),
            route('appointments.create'),
            route('queue.create'),
            route('consultations.create'),
            route('bpjs.create'),
            route('pharmacy.index'),
            route('account.show'),
        ] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_patient_service_pages_are_separate_and_personalized(): void
    {
        $patient = User::factory()->create();

        $this->actingAs($patient)->get(route('appointments.create'))
            ->assertOk()
            ->assertSee('Buat janji temu');

        $this->get(route('queue.create'))
            ->assertOk()
            ->assertSee('Permohonan antrean');

        $this->get(route('consultations.create'))
            ->assertOk()
            ->assertSee('Permohonan konsultasi');

        $this->get(route('bpjs.create'))
            ->assertOk()
            ->assertSee('Kunjungan dengan BPJS');

        $this->get(route('pharmacy.index'))
            ->assertOk()
            ->assertSee('Katalog farmasi')
            ->assertDontSee('Konsultasi online');

        $this->get(route('account.show'))
            ->assertOk()
            ->assertSee($patient->name);
    }

    public function test_patient_dashboard_does_not_disclose_another_patients_records(): void
    {
        $patient = User::factory()->create();
        $otherPatient = User::factory()->create();

        $ownAppointment = $patient->appointments()->create($this->appointmentAttributes('RS-OWN-1111'));
        $otherPatient->appointments()->create($this->appointmentAttributes('RS-OTHER-9999'));
        $patient->pharmacyOrders()->create($this->orderAttributes('FAR-OWN-1111'));
        $otherPatient->pharmacyOrders()->create($this->orderAttributes('FAR-OTHER-9999'));

        $this->actingAs($patient)->get(route('home'))
            ->assertOk()
            ->assertSee($ownAppointment->appointment_code)
            ->assertSee('FAR-OWN-1111')
            ->assertDontSee('RS-OTHER-9999')
            ->assertDontSee('FAR-OTHER-9999');
    }

    public function test_patient_dashboard_counts_only_unconfirmed_requests(): void
    {
        $patient = User::factory()->create();
        $patient->appointments()->create(array_merge($this->appointmentAttributes('RS-CONFIRMED-01'), [
            'status' => 'dikonfirmasi',
        ]));
        $patient->pharmacyOrders()->create(array_merge($this->orderAttributes('FAR-CONFIRMED-01'), [
            'status' => 'dikonfirmasi',
        ]));
        $patient->appointments()->create(array_merge($this->appointmentAttributes('RS-PENDING-001'), [
            'status' => 'menunggu_konfirmasi',
        ]));

        $this->actingAs($patient)->get(route('home'))
            ->assertSee('1 permohonan menunggu petugas');
    }

    public function test_patient_can_create_a_pharmacy_request_from_the_server_side_cart(): void
    {
        $patient = User::factory()->create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
        ]);

        $this->actingAs($patient)->from(route('pharmacy.index'))->post(route('pharmacy.checkout'), [
            'items' => ['vitamin-c' => '2', 'oralit' => '0'],
        ])->assertRedirect(route('pharmacy-orders.create'));

        $this->get(route('pharmacy-orders.create'))
            ->assertOk()
            ->assertSee('Vitamin C 500 mg')
            ->assertSee('Rp56.000');

        $this->post(route('pharmacy-orders.store'), [
            'fulfillment' => 'pickup',
            'privacy_consent' => '1',
            'items' => ['vitamin-c' => 10],
            'total' => 1,
            'user_id' => 999,
        ])->assertRedirect(route('home'));

        $order = PharmacyOrder::query()->firstOrFail();
        $this->assertSame($patient->id, $order->user_id);
        $this->assertSame('Budi Santoso', $order->patient_name);
        $this->assertSame('081234567890', $order->phone);
        $this->assertNotSame('081234567890', DB::table('pharmacy_orders')->value('phone'));
        $this->assertSame(56000, $order->total);
        $this->assertSame([[
            'sku' => 'vitamin-c',
            'name' => 'Vitamin C 500 mg',
            'quantity' => 2,
            'unit_price' => 28000,
        ]], $order->items);
    }

    public function test_catalogue_includes_new_medicines_and_mobility_equipment(): void
    {
        $patient = User::factory()->create();

        $this->actingAs($patient)->get(route('pharmacy.index'))
            ->assertSee('Sirup obat batuk non-resep')
            ->assertSee('Bodrex tablet')
            ->assertSee('Tongkat jalan')
            ->assertSee('Kursi roda standar')
            ->assertSee('Termometer digital');
    }

    public function test_patient_can_order_new_products_at_server_catalogue_prices(): void
    {
        $patient = User::factory()->create();

        $this->actingAs($patient)->post(route('pharmacy.checkout'), [
            'items' => ['bodrex' => '2', 'kursi-roda' => '1'],
        ])->assertRedirect(route('pharmacy-orders.create'));

        $this->get(route('pharmacy-orders.create'))
            ->assertSee('Bodrex tablet')
            ->assertSee('Rp860.000');

        $this->post(route('pharmacy-orders.store'), [
            'fulfillment' => 'pickup',
            'privacy_consent' => '1',
        ])->assertRedirect(route('home'));

        $order = PharmacyOrder::query()->firstOrFail();
        $this->assertSame(860000, $order->total);
        $this->assertSame([
            [
                'sku' => 'bodrex',
                'name' => 'Bodrex tablet',
                'quantity' => 2,
                'unit_price' => 5000,
            ],
            [
                'sku' => 'kursi-roda',
                'name' => 'Kursi roda standar',
                'quantity' => 1,
                'unit_price' => 850000,
            ],
        ], $order->items);
    }

    public function test_guest_cannot_start_a_pharmacy_checkout_or_submit_an_order(): void
    {
        $this->post(route('pharmacy.checkout'), [
            'items' => ['vitamin-c' => 1],
        ])->assertRedirect(route('login'));

        $this->post(route('pharmacy-orders.store'), [
            'fulfillment' => 'pickup',
            'privacy_consent' => '1',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('pharmacy_orders', 0);
    }

    public function test_unknown_product_is_rejected_from_a_patient_cart(): void
    {
        $patient = User::factory()->create();

        $this->actingAs($patient)->from(route('pharmacy.index'))->post(route('pharmacy.checkout'), [
            'items' => ['not-a-product' => '1'],
        ])->assertRedirect(route('pharmacy.index'))
            ->assertSessionHasErrors(['items'], null, 'pharmacy');

        $this->assertDatabaseCount('pharmacy_orders', 0);
    }

    public function test_empty_pharmacy_cart_cannot_be_checked_out(): void
    {
        $patient = User::factory()->create();

        $this->actingAs($patient)->post(route('pharmacy-orders.store'), [
            'fulfillment' => 'pickup',
            'privacy_consent' => '1',
        ])->assertRedirect(route('pharmacy.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('pharmacy_orders', 0);
    }

    private function appointmentAttributes(string $code): array
    {
        return [
            'appointment_code' => $code,
            'patient_name' => 'Test Patient',
            'phone' => '081234567890',
            'service_type' => 'appointment',
            'specialty' => 'umum',
            'visit_date' => today()->addDay(),
            'time_slot' => '08.00–10.00',
            'insurance' => 'umum',
            'status' => 'menunggu_konfirmasi',
            'consent_accepted' => true,
        ];
    }

    private function orderAttributes(string $code): array
    {
        return [
            'order_code' => $code,
            'patient_name' => 'Test Patient',
            'phone' => '081234567890',
            'items' => [['sku' => 'vitamin-c', 'name' => 'Vitamin C 500 mg', 'quantity' => 1, 'unit_price' => 28000]],
            'fulfillment' => 'pickup',
            'status' => 'menunggu_konfirmasi',
            'total' => 28000,
            'consent_accepted' => true,
        ];
    }
}
