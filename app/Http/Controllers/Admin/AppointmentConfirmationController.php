<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentConfirmationController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::query()
            ->with(['user:id,name,email', 'confirmedBy:id,name'])
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', ['menunggu_konfirmasi'])
            ->latest()
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.appointments.index', [
            'appointments' => $appointments,
        ]);
    }

    public function confirm(Appointment $appointment, Request $request): RedirectResponse
    {
        $updated = Appointment::query()
            ->whereKey($appointment->getKey())
            ->where('status', 'menunggu_konfirmasi')
            ->update([
                'status' => 'dikonfirmasi',
                'confirmed_by_user_id' => $request->user()->id,
                'confirmed_at' => now(),
            ]);

        if ($updated === 0) {
            return back()->with('error', 'Kunjungan ini sudah diproses dan tidak lagi menunggu konfirmasi.');
        }

        return back()->with('status', 'Kunjungan '.$appointment->appointment_code.' berhasil dikonfirmasi.');
    }
}
