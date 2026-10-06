<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PharmacyOrderController extends Controller
{
    public function index(): View
    {
        return view('portal.pharmacy.index', [
            'products' => config('pharmacy.products'),
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('pharmacy', [
            'items' => ['required', 'array', 'min:1', 'max:14'],
            'items.*' => ['required', 'integer', 'min:0', 'max:10'],
        ], [
            'items.required' => 'Pilih setidaknya satu produk.',
            'items.array' => 'Daftar produk tidak valid.',
            'items.min' => 'Pilih setidaknya satu produk.',
            'items.max' => 'Maksimal 14 jenis produk per pesanan.',
            'items.*.integer' => 'Jumlah produk harus berupa angka.',
            'items.*.min' => 'Jumlah produk tidak valid.',
            'items.*.max' => 'Jumlah setiap produk maksimal 10.',
        ]);

        $validated['items'] = array_map(
            fn (string|int $quantity): int => (int) $quantity,
            array_filter($validated['items'], fn (string|int $quantity): bool => (int) $quantity > 0),
        );
        if ($validated['items'] === []) {
            throw ValidationException::withMessages([
                'items' => 'Pilih setidaknya satu produk.',
            ])->errorBag('pharmacy');
        }

        $products = config('pharmacy.products');
        if (array_diff(array_keys($validated['items']), array_keys($products)) !== []) {
            throw ValidationException::withMessages([
                'items' => 'Salah satu produk yang dipilih tidak tersedia.',
            ])->errorBag('pharmacy');
        }

        $request->session()->put('pharmacy_cart', $validated['items']);

        return to_route('pharmacy-orders.create');
    }

    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('pharmacy_cart', []);
        if ($cart === []) {
            return to_route('pharmacy.index')->with('error', 'Pilih produk farmasi sebelum melanjutkan.');
        }

        $products = config('pharmacy.products');
        $items = [];
        $total = 0;
        foreach ($cart as $sku => $quantity) {
            if (! isset($products[$sku]) || ! is_int($quantity) || $quantity < 1 || $quantity > 10) {
                $request->session()->forget('pharmacy_cart');

                return to_route('pharmacy.index')->with('error', 'Keranjang berubah. Silakan pilih produk kembali.');
            }

            $item = $products[$sku];
            $items[] = [
                'sku' => $sku,
                'name' => $item['name'],
                'quantity' => $quantity,
                'unit_price' => $item['price'],
            ];
            $total += $item['price'] * $quantity;
        }

        return view('portal.pharmacy.checkout', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('pharmacy', [
            'fulfillment' => ['required', Rule::in(['pickup', 'delivery'])],
            'delivery_address' => ['required_if:fulfillment,delivery', 'nullable', 'string', 'max:500'],
            'privacy_consent' => ['accepted'],
        ], [
            'fulfillment.required' => 'Pilih metode pemenuhan pesanan.',
            'fulfillment.in' => 'Metode pemenuhan pesanan tidak tersedia.',
            'delivery_address.required_if' => 'Alamat pengantaran wajib diisi.',
            'delivery_address.max' => 'Alamat pengantaran maksimal 500 karakter.',
            'privacy_consent.accepted' => 'Persetujuan penggunaan data wajib disetujui.',
        ]);

        $cart = $request->session()->get('pharmacy_cart', []);
        if ($cart === []) {
            return to_route('pharmacy.index')->with('error', 'Keranjang kosong atau kedaluwarsa. Silakan pilih produk kembali.');
        }

        $products = config('pharmacy.products');
        if (array_diff(array_keys($cart), array_keys($products)) !== []) {
            $request->session()->forget('pharmacy_cart');

            return to_route('pharmacy.index')->with('error', 'Produk tidak tersedia. Silakan pilih kembali.');
        }

        $items = [];
        $total = 0;
        foreach ($cart as $sku => $quantity) {
            if (! is_int($quantity) || $quantity < 1 || $quantity > 10) {
                $request->session()->forget('pharmacy_cart');

                return to_route('pharmacy.index')->with('error', 'Jumlah produk tidak valid. Silakan pilih kembali.');
            }

            $product = $products[$sku];
            $items[] = [
                'sku' => $sku,
                'name' => $product['name'],
                'quantity' => $quantity,
                'unit_price' => $product['price'],
            ];
            $total += $product['price'] * $quantity;
        }

        $order = DB::transaction(fn () => $request->user()->pharmacyOrders()->create([
            'order_code' => 'FAR-'.now()->format('ymd').'-'.strtoupper(Str::random(8)),
            'patient_name' => $request->user()->name,
            'phone' => $request->user()->phone,
            'items' => $items,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'fulfillment' => $validated['fulfillment'],
            'status' => 'menunggu_konfirmasi',
            'total' => $total,
            'consent_accepted' => true,
        ]));

        $request->session()->forget('pharmacy_cart');

        return to_route('home')->with('status', 'Permohonan farmasi tersimpan dengan kode '.$order->order_code.'.');
    }
}
