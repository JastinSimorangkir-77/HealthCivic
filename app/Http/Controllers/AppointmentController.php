<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $patient = $request->user();

        if ($patient->hasRole(UserRole::Admin, UserRole::Doctor)) {
            return to_route('admin.appointments.index');
        }

        if ($patient->hasRole(UserRole::PharmacyAdmin)) {
            return to_route('admin.pharmacy-orders.index');
        }

        return view('portal.dashboard', [
            'appointments' => $patient->appointments()->latest()->limit(5)->get(),
            'orders' => $patient->pharmacyOrders()->latest()->limit(5)->get(),
            'appointmentCount' => $patient->appointments()->count(),
            'orderCount' => $patient->pharmacyOrders()->count(),
            'pendingConfirmationCount' => $patient->appointments()->where('status', 'menunggu_konfirmasi')->count()
                + $patient->pharmacyOrders()->where('status', 'menunggu_konfirmasi')->count(),
        ]);
    }

    public function createAppointment(): View
    {
        return $this->appointmentForm('appointment');
    }

    public function createQueue(): View
    {
        return $this->appointmentForm('queue');
    }

    public function createConsultation(): View
    {
        return $this->appointmentForm('consultation');
    }

    public function createBpjs(): View
    {
        return $this->appointmentForm('bpjs');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('appointment', [
            'service_type' => ['required', Rule::in(['appointment', 'queue', 'consultation', 'bpjs'])],
            'specialty' => ['required', Rule::in(['umum', 'penyakit-dalam', 'anak', 'gigi', 'kulit', 'kandungan', 'mata'])],
            'doctor' => ['nullable', 'string', 'max:120'],
            'visit_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.today()->addDays(90)->toDateString()],
            'time_slot' => ['required', Rule::in(['08.00–10.00', '10.00–12.00', '13.00–15.00', '15.00–17.00'])],
            'insurance' => [
                'required',
                Rule::in($request->input('service_type') === 'bpjs'
                    ? ['bpjs']
                    : ['umum', 'bpjs', 'asuransi']),
            ],
            'bpjs_number' => [
                Rule::requiredIf(fn () => $request->input('insurance') === 'bpjs'
                    || $request->input('service_type') === 'bpjs'),
                'nullable',
                'string',
                'max:30',
            ],
            'notes' => ['exclude_unless:service_type,consultation', 'nullable', 'string', 'max:2000'],
            'privacy_consent' => ['accepted'],
        ], [
            'service_type.required' => 'Jenis layanan tidak valid.',
            'service_type.in' => 'Jenis layanan tidak tersedia.',
            'specialty.required' => 'Pilih poli tujuan.',
            'specialty.in' => 'Poli yang dipilih tidak tersedia.',
            'doctor.max' => 'Nama dokter maksimal 120 karakter.',
            'visit_date.required' => 'Pilih tanggal kunjungan.',
            'visit_date.date' => 'Tanggal kunjungan tidak valid.',
            'visit_date.after_or_equal' => 'Tanggal kunjungan tidak boleh di masa lalu.',
            'visit_date.before_or_equal' => 'Pemesanan hanya tersedia hingga 90 hari ke depan.',
            'time_slot.required' => 'Pilih waktu kunjungan.',
            'time_slot.in' => 'Waktu kunjungan yang dipilih tidak tersedia.',
            'insurance.required' => 'Pilih metode pembayaran.',
            'insurance.in' => 'Pilih BPJS sebagai metode pembayaran untuk layanan BPJS.',
            'bpjs_number.required_if' => 'Nomor peserta BPJS wajib diisi untuk pembayaran BPJS.',
            'bpjs_number.string' => 'Nomor peserta BPJS tidak valid.',
            'bpjs_number.max' => 'Nomor peserta BPJS maksimal 30 karakter.',
            'notes.max' => 'Catatan konsultasi maksimal 2.000 karakter.',
            'privacy_consent.accepted' => 'Persetujuan penggunaan data wajib disetujui.',
        ]);

        $appointment = DB::transaction(fn () => $request->user()->appointments()->create([
            'appointment_code' => 'RS-'.now()->format('ymd').'-'.strtoupper(Str::random(8)),
            'patient_name' => $request->user()->name,
            'phone' => $request->user()->phone,
            'service_type' => $validated['service_type'],
            'specialty' => $validated['specialty'],
            'doctor' => $validated['doctor'] ?? null,
            'visit_date' => $validated['visit_date'],
            'time_slot' => $validated['time_slot'],
            'insurance' => $validated['insurance'],
            'bpjs_number' => $validated['bpjs_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'menunggu_konfirmasi',
            'consent_accepted' => true,
        ]));

        return to_route('home')->with('status', 'Permohonan tersimpan dengan kode '.$appointment->appointment_code.'.');
    }

    private function appointmentForm(string $serviceType): View
    {
        $service = match ($serviceType) {
            'queue' => ['title' => 'Permohonan antrean', 'description' => 'Ajukan kunjungan rawat jalan. Nomor antrean operasional belum tersedia sebelum integrasi rumah sakit aktif.'],
            'consultation' => ['title' => 'Permohonan konsultasi', 'description' => 'Ajukan konsultasi jarak jauh. Informasi yang Anda tulis akan disimpan terenkripsi dan belum diajukan kepada tenaga medis.'],
            'bpjs' => ['title' => 'Kunjungan dengan BPJS', 'description' => 'Ajukan kunjungan BPJS. Verifikasi kepesertaan dan rujukan masih harus dilakukan melalui sistem resmi rumah sakit.'],
            default => ['title' => 'Buat janji temu', 'description' => 'Pilih poli serta waktu kunjungan yang diinginkan. Jadwal masih memerlukan konfirmasi petugas.'],
        };

        return view('portal.appointments.create', [
            'serviceType' => $serviceType,
            'service' => $service,
            'specialties' => [
                'umum' => 'Poli umum',
                'penyakit-dalam' => 'Penyakit dalam',
                'anak' => 'Kesehatan anak',
                'gigi' => 'Kesehatan gigi',
                'kulit' => 'Kulit dan kelamin',
                'kandungan' => 'Kebidanan dan kandungan',
                'mata' => 'Kesehatan mata',
            ],
            'timeSlots' => ['08.00–10.00', '10.00–12.00', '13.00–15.00', '15.00–17.00'],
        ]);
    }
}
