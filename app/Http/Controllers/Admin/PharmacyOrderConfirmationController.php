<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PharmacyOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacyOrderConfirmationController extends Controller
{
    public function index(): View
    {
        $orders = PharmacyOrder::query()
            ->with(['user:id,name,email', 'confirmedBy:id,name'])
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', ['menunggu_konfirmasi'])
            ->latest()
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.pharmacy-orders.index', [
            'orders' => $orders,
        ]);
    }

    public function confirm(PharmacyOrder $pharmacyOrder, Request $request): RedirectResponse
    {
        $updated = PharmacyOrder::query()
            ->whereKey($pharmacyOrder->getKey())
            ->where('status', 'menunggu_konfirmasi')
            ->update([
                'status' => 'dikonfirmasi',
                'confirmed_by_user_id' => $request->user()->id,
                'confirmed_at' => now(),
            ]);

        if ($updated === 0) {
            return back()->with('error', 'Pesanan ini sudah diproses dan tidak lagi menunggu konfirmasi.');
        }

        return back()->with('status', 'Pesanan '.$pharmacyOrder->order_code.' berhasil dikonfirmasi.');
    }
}
