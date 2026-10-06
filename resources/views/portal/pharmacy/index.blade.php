@extends('layouts.portal')

@section('title', 'Farmasi')
@section('section', 'Farmasi')

@section('content')
    <section class="page-heading">
        <div><span class="eyebrow">FARMASI RS SEHAT SENTOSA</span><h1>Katalog farmasi</h1><p>Pilih produk contoh untuk membuat permohonan pesanan. Ketersediaan dan harga perlu dikonfirmasi.</p></div>
        <a class="button button-secondary" href="{{ route('home') }}">← Kembali ke ringkasan</a>
    </section>
    <div class="alert alert-info" role="note"><strong>Katalog demonstrasi</strong><span>Produk dan harga di bawah adalah contoh, bukan stok atau harga resmi, dan tidak dapat digunakan sebagai rekomendasi medis. Resep, pembayaran, dan pengantaran belum terintegrasi.</span></div>

    <section class="form-panel">
        <div class="form-panel-heading"><span class="form-panel-icon">＋</span><div><h2>Pilih produk</h2><p>Masukkan jumlah setiap produk, lalu lanjutkan untuk memeriksa permohonan.</p></div></div>
        <form method="POST" action="{{ route('pharmacy.checkout') }}">
            @csrf
            <div class="product-grid pharmacy-grid">
                @foreach ($products as $sku => $product)
                    <article class="product-card">
                        <div class="product-art product-art-{{ $product['art'] }}"><span class="product-symbol">{{ $product['symbol'] }}</span><span class="product-art-label">{{ $product['art_label'] }}</span></div>
                        <div class="product-details"><span class="product-category">{{ $product['category'] }}</span><h3>{{ $product['name'] }}</h3><p>{{ $product['description'] }}</p><div class="product-buy"><strong>Rp{{ number_format($product['price'], 0, ',', '.') }}</strong><label class="quantity-field">Jumlah<input type="number" name="items[{{ $sku }}]" value="{{ old('items.'.$sku, 0) }}" min="0" max="10" aria-label="Jumlah {{ $product['name'] }}"></label></div></div>
                    </article>
                @endforeach
            </div>
            <div class="pharmacy-actions"><p>Pilih setidaknya satu produk. Maksimal 10 unit per produk.</p><button class="button button-primary" type="submit">Tinjau keranjang →</button></div>
        </form>
    </section>
    <p class="pharmacy-disclaimer">Jika membutuhkan obat resep, silakan konsultasikan dengan tenaga kesehatan dan ikuti resep resmi.</p>
@endsection
