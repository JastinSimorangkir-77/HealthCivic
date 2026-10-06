@extends('layouts.portal')

@section('title', 'Ringkasan')
@section('section', 'Ringkasan')

@section('content')
    <section class="page-heading">
        <div>
            <span class="eyebrow">RUANG PASIEN</span>
            <h1>Selamat datang, {{ auth()->user()->name }}</h1>
            <p>Kelola layanan dan lihat ringkasan permohonan Anda di sini.</p>
        </div>
        <a class="button button-primary" href="{{ route('appointments.create') }}">＋ Buat janji temu</a>
    </section>

    <div class="alert alert-info" role="note">
        <strong>Portal awal — belum untuk layanan pasien nyata.</strong>
        <span>Jadwal, antrean aktif, verifikasi BPJS, konsultasi klinis, stok farmasi, serta pembayaran belum terhubung ke sistem rumah sakit.</span>
    </div>

    <section class="summary-grid" aria-label="Ringkasan aktivitas akun">
        <article class="summary-card"><span class="summary-icon icon-teal">▦</span><span class="summary-label">PERMOHONAN KUNJUNGAN</span><strong>{{ $appointmentCount }}</strong><small>tersimpan di akun Anda</small></article>
        <article class="summary-card"><span class="summary-icon icon-blue">＋</span><span class="summary-label">PERMOHONAN FARMASI</span><strong>{{ $orderCount }}</strong><small>tersimpan di akun Anda</small></article>
        <article class="summary-card summary-callout"><span class="summary-icon icon-amber">✓</span><span class="summary-label">STATUS LAYANAN</span><strong>{{ $pendingConfirmationCount > 0 ? 'Menunggu konfirmasi' : 'Tidak ada tertunda' }}</strong><small>{{ $pendingConfirmationCount }} permohonan menunggu petugas</small></article>
    </section>

    <section class="dashboard-services">
        <div class="section-heading"><div><span class="eyebrow">AKSES CEPAT</span><h2>Layanan pasien</h2><p>Pilih layanan untuk membuka halaman khusus.</p></div></div>
        <div class="service-grid">
            <a class="service-card service-appointment" href="{{ route('appointments.create') }}"><span class="service-icon">▦</span><span class="service-tag">RAWAT JALAN</span><h3>Janji temu</h3><p>Pilih poli dan ajukan jadwal kunjungan.</p><span class="service-link">Buka layanan →</span></a>
            <a class="service-card service-queue" href="{{ route('queue.create') }}"><span class="service-icon">▤</span><span class="service-tag">ANTREAN</span><h3>Antrean rawat jalan</h3><p>Ajukan kunjungan sebelum datang ke rumah sakit.</p><span class="service-link">Buka layanan →</span></a>
            <a class="service-card service-consult" href="{{ route('consultations.create') }}"><span class="service-icon">◌</span><span class="service-tag">KONSULTASI</span><h3>Konsultasi</h3><p>Ajukan permohonan konsultasi jarak jauh.</p><span class="service-link">Buka layanan →</span></a>
            <a class="service-card service-pharmacy" href="{{ route('pharmacy.index') }}"><span class="service-icon">＋</span><span class="service-tag">FARMASI</span><h3>Farmasi</h3><p>Lihat katalog contoh dan buat permohonan.</p><span class="service-link">Buka layanan →</span></a>
            <a class="service-card service-queue" href="{{ route('bpjs.create') }}"><span class="service-icon">▣</span><span class="service-tag">PENJAMIN</span><h3>BPJS & asuransi</h3><p>Ajukan kunjungan dengan metode penjamin.</p><span class="service-link">Buka layanan →</span></a>
        </div>
    </section>

    <section class="data-panel">
        <div class="section-heading"><div><span class="eyebrow">AKTIVITAS TERBARU</span><h2>Permohonan kunjungan Anda</h2><p>Hanya catatan milik akun Anda yang ditampilkan.</p></div><a class="text-link" href="{{ route('appointments.create') }}">Buat permohonan →</a></div>
        @if ($appointments->isEmpty())
            <div class="empty-state"><span class="empty-icon">▦</span><strong>Belum ada permohonan kunjungan</strong><p>Permohonan yang Anda buat akan tampil di bagian ini.</p></div>
        @else
            <div class="table-wrap"><table class="data-table"><thead><tr><th>KODE</th><th>JENIS LAYANAN</th><th>POLI</th><th>TANGGAL</th><th>STATUS</th></tr></thead><tbody>
                @foreach ($appointments as $appointment)
                    <tr><td class="code-cell">{{ $appointment->appointment_code }}</td><td>{{ ['appointment' => 'Janji temu', 'queue' => 'Antrean', 'consultation' => 'Konsultasi', 'bpjs' => 'BPJS'][$appointment->service_type] ?? 'Kunjungan' }}</td><td>{{ $appointment->specialty }}</td><td>{{ $appointment->visit_date->format('d M Y') }}</td><td><span class="status-badge">{{ str_replace('_', ' ', $appointment->status) }}</span></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>

    <section class="data-panel">
        <div class="section-heading"><div><span class="eyebrow">FARMASI</span><h2>Permohonan farmasi Anda</h2><p>Pesanan contoh hanya terlihat oleh akun Anda.</p></div><a class="text-link" href="{{ route('pharmacy.index') }}">Buka katalog →</a></div>
        @if ($orders->isEmpty())
            <div class="empty-state"><span class="empty-icon">＋</span><strong>Belum ada permohonan farmasi</strong><p>Permohonan yang Anda buat akan tampil di bagian ini.</p></div>
        @else
            <div class="table-wrap"><table class="data-table"><thead><tr><th>KODE</th><th>JENIS PEMENUHAN</th><th>ESTIMASI TOTAL</th><th>STATUS</th></tr></thead><tbody>
                @foreach ($orders as $order)
                    <tr><td class="code-cell">{{ $order->order_code }}</td><td>{{ $order->fulfillment === 'pickup' ? 'Ambil di farmasi' : 'Informasi pengantaran' }}</td><td>Rp{{ number_format($order->total, 0, ',', '.') }}</td><td><span class="status-badge">{{ str_replace('_', ' ', $order->status) }}</span></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>

    <section class="safety-note"><strong>Keadaan darurat?</strong><span>Portal ini tidak dipantau tenaga klinis. Untuk keadaan darurat, segera datang ke IGD atau hubungi layanan darurat setempat.</span></section>
@endsection
