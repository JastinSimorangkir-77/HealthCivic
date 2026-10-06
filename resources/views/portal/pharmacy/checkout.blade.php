@extends('layouts.portal')

@section('title', 'Tinjau pesanan farmasi')
@section('section', 'Farmasi / Pesanan')

@section('content')
    <section class="page-heading">
        <div><span class="eyebrow">FARMASI</span><h1>Tinjau permohonan</h1><p>Periksa item dan metode pemenuhan sebelum mengirim permohonan ke rumah sakit.</p></div>
        <a class="button button-secondary" href="{{ route('pharmacy.index') }}">← Kembali ke katalog</a>
    </section>
    <div class="alert alert-info" role="note"><strong>Belum ada pembayaran</strong><span>Stok, harga, resep, dan pengantaran belum terintegrasi. Petugas perlu memverifikasi sebelum ada pembelian.</span></div>
    <section class="form-panel checkout-panel">
        <div class="form-panel-heading"><span class="form-panel-icon">＋</span><div><h2>Item permohonan</h2><p>Harga berikut adalah perkiraan dari katalog demonstrasi.</p></div></div>
        <div class="table-wrap"><table class="data-table"><thead><tr><th>PRODUK</th><th>JUMLAH</th><th>HARGA</th><th>SUBTOTAL</th></tr></thead><tbody>
            @foreach ($items as $item)
                <tr><td>{{ $item['name'] }}</td><td>{{ $item['quantity'] }}</td><td>Rp{{ number_format($item['unit_price'], 0, ',', '.') }}</td><td>Rp{{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody></table></div>
        <div class="checkout-total"><span>Estimasi total</span><strong>Rp{{ number_format($total, 0, ',', '.') }}</strong></div>
        <div class="account-summary"><span>Atas nama</span><strong>{{ auth()->user()->name }}</strong><span>Kontak terenkripsi</span><strong>{{ auth()->user()->email }}</strong></div>
        <form method="POST" action="{{ route('pharmacy-orders.store') }}" class="stack-form">
            @csrf
            <div class="form-grid">
                <label class="field">Metode pemenuhan
                    <select name="fulfillment" required data-address-toggle="[data-delivery-address]"><option value="pickup" @selected(old('fulfillment', 'pickup') === 'pickup')>Ambil di rumah sakit</option><option value="delivery" @selected(old('fulfillment') === 'delivery')>Ajukan informasi pengantaran</option></select>
                </label>
                <label class="field" data-delivery-address>Alamat pengantaran <span class="field-optional">(wajib jika memilih pengantaran)</span>
                    <input name="delivery_address" value="{{ old('delivery_address') }}" maxlength="500" autocomplete="street-address" placeholder="Alamat lengkap untuk ditinjau petugas">
                </label>
            </div>
            <label class="consent-check"><input type="checkbox" name="privacy_consent" value="1" required><span>Saya menyetujui penggunaan data akun dan alamat untuk menindaklanjuti permohonan ini.</span></label>
            <button class="button button-primary button-submit" type="submit">Ajukan permohonan →</button>
        </form>
    </section>
@endsection
