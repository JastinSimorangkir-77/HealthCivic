<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f8f7">
    <meta name="description" content="Portal layanan pasien RS Sehat Sentosa. Buat janji, ambil nomor antrean, dan pesan kebutuhan farmasi.">
    <title>Portal Pasien — RS Sehat Sentosa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-form-errors="{{ $errors->appointment->any() ? 'appointment' : ($errors->pharmacy->any() ? 'checkout' : '') }}" data-order-success="{{ $order ? 'true' : 'false' }}">
    <svg class="svg-library" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <symbol id="i-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M16 3v4M8 3v4M3 10h18"/></symbol>
        <symbol id="i-ticket" viewBox="0 0 24 24"><path d="M4 5h16v4a3 3 0 0 0 0 6v4H4v-4a3 3 0 0 0 0-6z"/><path d="M13 8v2m0 4v2"/></symbol>
        <symbol id="i-pills" viewBox="0 0 24 24"><path d="m10.5 20.5 9.2-9.2a5 5 0 0 0-7.1-7.1l-9.2 9.2a5 5 0 0 0 7.1 7.1Z"/><path d="m8 8 8 8"/></symbol>
        <symbol id="i-heart" viewBox="0 0 24 24"><path d="M20.8 8.8c0 5.3-8.8 11-8.8 11s-8.8-5.7-8.8-11A4.8 4.8 0 0 1 12 6a4.8 4.8 0 0 1 8.8 2.8Z"/><path d="M3.5 12h5l2-4 3.2 8 1.8-4h5"/></symbol>
        <symbol id="i-card" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18m-13 5h3"/></symbol>
        <symbol id="i-chat" viewBox="0 0 24 24"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8z"/></symbol>
        <symbol id="i-help" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9.6 9a2.5 2.5 0 1 1 4.4 1.6c-1.3 1-2 1.5-2 3m0 3h.01"/></symbol>
        <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m-6-6 6 6-6 6"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></symbol>
        <symbol id="i-bag" viewBox="0 0 24 24"><path d="M5 8h14l1 13H4L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></symbol>
        <symbol id="i-map" viewBox="0 0 24 24"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15m6-12v15"/></symbol>
        <symbol id="i-cross" viewBox="0 0 24 24"><path d="M10 3h4v7h7v4h-7v7h-4v-7H3v-4h7z"/></symbol>
        <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
        <symbol id="i-close" viewBox="0 0 24 24"><path d="m18 6-12 12M6 6l12 12"/></symbol>
    </svg>

    <div class="app-shell">
        <aside class="sidebar" aria-label="Navigasi utama">
            <a class="brand" href="#beranda" aria-label="RS Sehat Sentosa — beranda">
                <span class="brand-mark"><svg><use href="#i-cross"/></svg></span>
                <span class="brand-copy"><strong>sehat<span>sentosa</span></strong><small>RUMAH SAKIT</small></span>
            </a>
            <div class="side-label">MENU UTAMA</div>
            <nav class="side-nav">
                <a class="nav-link is-active" href="#beranda"><svg><use href="#i-grid"/></svg><span>Beranda</span></a>
                <a class="nav-link" href="#layanan"><svg><use href="#i-heart"/></svg><span>Layanan</span></a>
                <a class="nav-link" href="#janji"><svg><use href="#i-calendar"/></svg><span>Janji & antrean</span><span class="nav-dot"></span></a>
                <a class="nav-link" href="#farmasi"><svg><use href="#i-pills"/></svg><span>Farmasi</span></a>
                <a class="nav-link" href="#bpjs"><svg><use href="#i-card"/></svg><span>BPJS & asuransi</span></a>
            </nav>
            <div class="side-label side-label-spaced">INFORMASI</div>
            <nav class="side-nav">
                <a class="nav-link" href="#bantuan"><svg><use href="#i-help"/></svg><span>Bantuan</span></a>
            </nav>
            <div class="sidebar-bottom">
                <div class="privacy-card">
                    <span class="privacy-icon"><svg><use href="#i-shield"/></svg></span>
                    <strong>Privasi Anda penting</strong>
                    <p>Data formulir dilindungi dan hanya digunakan untuk layanan yang Anda pilih.</p>
                    <a href="#bantuan">Pelajari selengkapnya <svg><use href="#i-arrow"/></svg></a>
                </div>
                <div class="profile-mini">
                    <span class="avatar avatar-small">PS</span>
                    <span><strong>Portal Pasien</strong><small>Selamat datang</small></span>
                    <span class="profile-more" aria-hidden="true">···</span>
                </div>
            </div>
        </aside>

        <main class="main-content" id="beranda">
            <header class="topbar">
                <button class="mobile-menu icon-button" type="button" aria-label="Buka menu" data-menu-toggle><svg><use href="#i-menu"/></svg></button>
                <div class="breadcrumbs"><span>Portal pasien</span><span class="crumb-slash">/</span><strong>Beranda</strong></div>
                <div class="topbar-actions">
                    <span class="today-label">{{ \Illuminate\Support\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                    <span class="topbar-divider"></span>
                    <span class="avatar avatar-top">PS</span>
                </div>
            </header>

            <div class="page-wrap">
                <div class="alert alert-info" role="note">
                    <span class="info-icon info-blue"><svg><use href="#i-help"/></svg></span>
                    <div><strong>Portal awal — belum untuk pendaftaran pasien nyata</strong><span>Jam dan jadwal dokter, nomor antrean operasional, verifikasi BPJS, kontak resmi, stok obat, serta pembayaran belum terhubung ke sistem rumah sakit.</span></div>
                </div>
                @if ($errors->appointment->any() || $errors->pharmacy->any())
                    <div class="alert alert-error" role="alert">
                        <strong>Formulir belum dapat dikirim.</strong>
                        <span>{{ $errors->appointment->any() ? $errors->appointment->first() : $errors->pharmacy->first() }}</span>
                    </div>
                @endif
                @if ($appointment)
                    <div class="alert alert-success" role="status">
                        <span class="success-mark"><svg><use href="#i-check"/></svg></span>
                        <div><strong>Permohonan kunjungan tersimpan</strong><span>Kode {{ $appointment->appointment_code }} · {{ $appointment->visit_date->format('d M Y') }}, {{ $appointment->time_slot }} · Menunggu konfirmasi. Kode ini belum merupakan nomor antrean operasional.</span></div>
                        <button type="button" class="alert-close" aria-label="Tutup" data-dismiss-alert><svg><use href="#i-close"/></svg></button>
                    </div>
                @endif
                @if ($order)
                    <div class="alert alert-success" role="status">
                        <span class="success-mark"><svg><use href="#i-check"/></svg></span>
                        <div><strong>Permohonan farmasi tersimpan</strong><span>Kode {{ $order->order_code }} · {{ $order->fulfillment === 'pickup' ? 'Ambil di farmasi' : 'Pengantaran' }} · Total contoh Rp{{ number_format($order->total, 0, ',', '.') }}. Stok dan harga perlu dikonfirmasi; pembayaran belum diproses.</span></div>
                        <button type="button" class="alert-close" aria-label="Tutup" data-dismiss-alert><svg><use href="#i-close"/></svg></button>
                    </div>
                @endif

                <section class="welcome-row">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-dot"></span> LAYANAN PASIEN TERPADU</p>
                        <h1>Selamat datang,<br><span>kesehatan Anda prioritas kami.</span></h1>
                        <p class="welcome-copy">Akses layanan rumah sakit dengan lebih mudah. Kami siap mendampingi setiap langkah perjalanan sehat Anda.</p>
                    </div>
                    <div class="welcome-actions">
                        <button class="button button-primary" type="button" data-open-appointment="appointment"><svg><use href="#i-calendar"/></svg>Buat janji temu</button>
                        <a class="button button-secondary" href="#layanan">Jelajahi layanan <svg><use href="#i-arrow"/></svg></a>
                    </div>
                </section>

                <section class="hero-banner" aria-label="Informasi layanan rumah sakit">
                    <div class="hero-content">
                        <span class="hero-kicker"><span class="hero-pulse"></span> HADIR UNTUK KESEHATAN ANDA</span>
                        <h2>Perawatan terbaik,<br>dimulai dari sini.</h2>
                        <p>Atur kunjungan, pilih poli, dan rencanakan perawatan Anda dalam satu tempat.</p>
                        <button class="hero-link" type="button" data-open-appointment="queue">Ajukan antrean rawat jalan <span><svg><use href="#i-arrow"/></svg></span></button>
                    </div>
                    <div class="hero-art" aria-hidden="true">
                        <div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div>
                        <div class="hero-cross"><svg><use href="#i-cross"/></svg></div>
                        <div class="floating-note"><span class="floating-check"><svg><use href="#i-check"/></svg></span><span><strong>Lebih praktis</strong><small>Layanan dalam genggaman</small></span></div>
                        <div class="floating-heart"><svg><use href="#i-heart"/></svg></div>
                    </div>
                    <span class="hero-decoration"></span>
                </section>

                <section class="info-strip" aria-label="Informasi rumah sakit">
                    <div class="info-item"><span class="info-icon info-green"><svg><use href="#i-clock"/></svg></span><span><small>JAM LAYANAN · CONTOH</small><strong>Senin – Sabtu, 08.00 – 17.00</strong></span></div>
                    <span class="info-separator"></span>
                    <div class="info-item"><span class="info-icon info-blue"><svg><use href="#i-map"/></svg></span><span><small>LOKASI</small><strong>Informasi alamat rumah sakit</strong></span></div>
                    <a class="info-action" href="#bantuan" aria-label="Lihat informasi"><svg><use href="#i-arrow"/></svg></a>
                </section>

                <section class="section-block" id="layanan">
                    <div class="section-heading">
                        <div><p class="eyebrow">MUDAH & TERINTEGRASI</p><h2>Layanan untuk Anda</h2><p class="section-subtitle">Pilih layanan yang Anda butuhkan hari ini.</p></div>
                        <a class="text-link" href="#janji">Lihat semua layanan <svg><use href="#i-arrow"/></svg></a>
                    </div>
                    <div class="service-grid">
                        <article class="service-card service-queue">
                            <span class="service-icon"><svg><use href="#i-ticket"/></svg></span><span class="service-tag">RAWAT JALAN</span>
                            <h3>Antrean rawat jalan</h3><p>Ajukan kunjungan rawat jalan; nomor operasional belum tersedia.</p>
                            <button type="button" class="service-link" data-open-appointment="queue">Ajukan antrean <svg><use href="#i-arrow"/></svg></button>
                        </article>
                        <article class="service-card service-appointment">
                            <span class="service-icon"><svg><use href="#i-calendar"/></svg></span><span class="service-tag">JADWAL DOKTER</span>
                            <h3>Janji temu dokter</h3><p>Pilih poli dan waktu kunjungan yang sesuai kebutuhan.</p>
                            <button type="button" class="service-link" data-open-appointment="appointment">Buat janji <svg><use href="#i-arrow"/></svg></button>
                        </article>
                        <article class="service-card service-consult">
                            <span class="service-icon"><svg><use href="#i-chat"/></svg></span><span class="service-tag">KONSULTASI</span>
                            <h3>Konsultasi online</h3><p>Ajukan permohonan konsultasi jarak jauh; jadwal belum terhubung.</p>
                            <button type="button" class="service-link" data-open-appointment="consultation">Ajukan konsultasi <svg><use href="#i-arrow"/></svg></button>
                        </article>
                        <article class="service-card service-pharmacy">
                            <span class="service-icon"><svg><use href="#i-pills"/></svg></span><span class="service-tag">FARMASI</span>
                            <h3>Pesan kebutuhan farmasi</h3><p>Lihat katalog contoh produk bebas dan ajukan pesanan.</p>
                            <a class="service-link" href="#farmasi">Lihat katalog <svg><use href="#i-arrow"/></svg></a>
                        </article>
                    </div>
                </section>

                <section class="appointment-panel" id="janji">
                    <div class="appointment-copy">
                        <p class="eyebrow eyebrow-light">RENCANAKAN KUNJUNGAN</p>
                        <h2>Langkah sehat dimulai<br>dari janji yang tepat.</h2>
                        <p>Pilih poli dan jadwal kunjungan tanpa perlu menunggu lama di rumah sakit.</p>
                        <button type="button" class="button button-white" data-open-appointment="appointment">Jadwalkan kunjungan <svg><use href="#i-arrow"/></svg></button>
                    </div>
                    <div class="appointment-side">
                        <div class="next-visit-icon"><svg><use href="#i-calendar"/></svg></div>
                        <span class="visit-caption">JADWAL KUNJUNGAN</span>
                        <strong>Poli pilihan Anda</strong>
                        <span class="visit-note">Pilih jadwal yang tersedia dalam formulir pendaftaran.</span>
                        <div class="visit-rule"></div>
                        <div class="visit-meta"><span><svg><use href="#i-clock"/></svg>Jadwal belum terhubung</span><span class="visit-badge"><span></span>Belum terintegrasi</span></div>
                    </div>
                    <div class="panel-decoration"></div>
                </section>

                <section class="section-block" id="farmasi">
                    <div class="section-heading pharmacy-heading">
                        <div><p class="eyebrow">FARMASI RS SEHAT SENTOSA</p><h2>Kebutuhan kesehatan, lebih mudah</h2><p class="section-subtitle">Katalog contoh produk. Permohonan pesanan belum terhubung ke sistem farmasi.</p></div>
                        <button class="button button-outline cart-open" type="button" data-open-cart><svg><use href="#i-bag"/></svg>Keranjang <span class="cart-count" data-cart-count>0</span></button>
                    </div>
                    <div class="product-grid">
                        @foreach ($products as $sku => $product)
                            <article class="product-card" data-product data-sku="{{ $sku }}" data-name="{{ $product['name'] }}" data-price="{{ $product['price'] }}">
                                <div class="product-art product-art-{{ $product['art'] }}"><span class="product-symbol">{{ $product['symbol'] }}</span><span class="product-art-label">{{ $product['art_label'] }}</span></div>
                                <div class="product-details"><span class="product-category">{{ $product['category'] }}</span><h3>{{ $product['name'] }}</h3><p>{{ $product['description'] }}</p><div class="product-buy"><strong>Rp{{ number_format($product['price'], 0, ',', '.') }}</strong><button type="button" class="add-button" aria-label="Tambah {{ $product['name'] }} ke keranjang" data-add-item="{{ $sku }}"><svg><use href="#i-plus"/></svg></button></div></div>
                            </article>
                        @endforeach
                    </div>
                    <p class="pharmacy-disclaimer"><svg><use href="#i-shield"/></svg>Informasi produk dan harga di atas adalah contoh, bukan rekomendasi medis atau harga resmi. Penggunaan obat harus mengikuti petunjuk tenaga kesehatan.</p>
                </section>

                <section class="bpjs-banner" id="bpjs">
                    <span class="bpjs-icon"><svg><use href="#i-card"/></svg></span>
                    <div class="bpjs-copy"><p class="eyebrow">PASIEN BPJS & ASURANSI</p><h2>Gunakan jaminan kesehatan Anda</h2><p>Ajukan kunjungan dengan BPJS atau asuransi. Verifikasi kepesertaan dan rujukan belum terhubung ke sistem penjamin.</p></div>
                    <button type="button" class="button button-dark" data-open-appointment="bpjs">Daftar kunjungan <svg><use href="#i-arrow"/></svg></button>
                </section>

                <section class="help-panel" id="bantuan">
                    <div class="help-icon"><svg><use href="#i-help"/></svg></div>
                    <div><h2>Butuh bantuan?</h2><p>Untuk jadwal, antrean, keadaan darurat, atau pesanan, gunakan kanal resmi rumah sakit setelah nomor kontak operasional dikonfigurasi.</p></div>
                    <span class="help-label">Informasi kontak resmi perlu dikonfigurasi</span>
                </section>

                <footer class="page-footer">
                    <span>© {{ now()->year }} RS Sehat Sentosa · Contoh aplikasi portal pasien</span>
                    <span>Untuk keadaan darurat, segera kunjungi IGD terdekat.</span>
                </footer>
            </div>
        </main>
    </div>

    <nav class="mobile-nav" aria-label="Navigasi cepat">
        <a class="is-active" href="#beranda"><svg><use href="#i-grid"/></svg><span>Beranda</span></a>
        <a href="#layanan"><svg><use href="#i-heart"/></svg><span>Layanan</span></a>
        <a href="#janji"><svg><use href="#i-calendar"/></svg><span>Janji</span></a>
        <a href="#farmasi"><svg><use href="#i-pills"/></svg><span>Farmasi</span></a>
    </nav>

    <dialog class="modal" id="appointment-dialog" aria-labelledby="appointment-title">
        <form method="POST" action="{{ route('appointments.store') }}" class="modal-form">
            @csrf
            <button type="button" class="modal-close" aria-label="Tutup" data-close-dialog><svg><use href="#i-close"/></svg></button>
            <span class="modal-icon"><svg><use href="#i-calendar"/></svg></span>
            <p class="eyebrow">RS SEHAT SENTOSA</p>
            <h2 id="appointment-title">Rencanakan kunjungan</h2>
            <p class="modal-intro">Isi data berikut. Permohonan akan dicatat untuk ditindaklanjuti setelah sistem operasional rumah sakit terhubung.</p>
            <input type="hidden" name="service_type" value="{{ old('service_type', 'appointment') }}" data-service-type>
            <div class="form-grid">
                <label class="field field-full">Nama lengkap pasien<input name="patient_name" autocomplete="name" maxlength="120" required placeholder="Sesuai kartu identitas" value="{{ old('patient_name') }}"></label>
                <label class="field field-full">Nomor telepon<input name="phone" type="tel" autocomplete="tel" maxlength="25" required placeholder="Contoh: 0812 3456 7890" value="{{ old('phone') }}"></label>
                <label class="field">Poli tujuan<select name="specialty" required><option value="">Pilih poli</option><option value="umum" @selected(old('specialty') === 'umum')>Poli umum</option><option value="penyakit-dalam" @selected(old('specialty') === 'penyakit-dalam')>Penyakit dalam</option><option value="anak" @selected(old('specialty') === 'anak')>Kesehatan anak</option><option value="gigi" @selected(old('specialty') === 'gigi')>Kesehatan gigi</option><option value="kulit" @selected(old('specialty') === 'kulit')>Kulit dan kelamin</option><option value="kandungan" @selected(old('specialty') === 'kandungan')>Kebidanan dan kandungan</option><option value="mata" @selected(old('specialty') === 'mata')>Kesehatan mata</option></select></label>
                <label class="field">Dokter (opsional)<input name="doctor" maxlength="120" placeholder="Nama dokter pilihan" value="{{ old('doctor') }}"></label>
                <label class="field">Tanggal kunjungan<input name="visit_date" type="date" min="{{ today()->toDateString() }}" max="{{ today()->addDays(90)->toDateString() }}" value="{{ old('visit_date') }}" required></label>
                <label class="field">Waktu kunjungan<select name="time_slot" required><option value="">Pilih waktu</option><option value="08.00–10.00" @selected(old('time_slot') === '08.00–10.00')>08.00 – 10.00</option><option value="10.00–12.00" @selected(old('time_slot') === '10.00–12.00')>10.00 – 12.00</option><option value="13.00–15.00" @selected(old('time_slot') === '13.00–15.00')>13.00 – 15.00</option><option value="15.00–17.00" @selected(old('time_slot') === '15.00–17.00')>15.00 – 17.00</option></select></label>
                <label class="field field-full">Metode pembayaran<select name="insurance" required data-insurance><option value="">Pilih metode</option><option value="umum" @selected(old('insurance') === 'umum')>Umum / mandiri</option><option value="bpjs" @selected(old('insurance') === 'bpjs')>BPJS Kesehatan</option><option value="asuransi" @selected(old('insurance') === 'asuransi')>Asuransi swasta</option></select></label>
                <label class="field field-full is-hidden" data-bpjs-field>Nomor peserta BPJS<input name="bpjs_number" maxlength="30" autocomplete="off" placeholder="Masukkan nomor peserta BPJS" value="{{ old('bpjs_number') }}"></label>
            </div>
            <label class="consent-check"><input type="checkbox" name="privacy_consent" value="1" required><span>Saya menyetujui penggunaan data untuk pemrosesan pendaftaran ini. Data identitas disimpan dalam bentuk terenkripsi.</span></label>
            <p class="form-note">Jadwal dan nomor antrean belum terhubung ke sistem rumah sakit. Verifikasi BPJS/rujukan juga belum tersedia.</p>
            <button class="button button-primary button-submit" type="submit">Kirim permohonan <svg><use href="#i-arrow"/></svg></button>
        </form>
    </dialog>

    <dialog class="modal modal-cart" id="cart-dialog" aria-labelledby="cart-title">
        <div class="modal-form">
            <button type="button" class="modal-close" aria-label="Tutup" data-close-dialog><svg><use href="#i-close"/></svg></button>
            <span class="modal-icon"><svg><use href="#i-bag"/></svg></span>
            <p class="eyebrow">FARMASI</p>
            <h2 id="cart-title">Keranjang Anda</h2>
            <p class="modal-intro">Tinjau produk sebelum melanjutkan permohonan pesanan.</p>
            <div class="cart-items" data-cart-items></div>
            <div class="cart-total"><span>Estimasi total</span><strong data-cart-total>Rp0</strong></div>
            <button class="button button-primary button-submit" type="button" data-checkout disabled>Lanjutkan pesanan <svg><use href="#i-arrow"/></svg></button>
            <p class="form-note">Ini pratinjau permohonan. Ketersediaan dan harga perlu dikonfirmasi secara manual sebelum pembelian.</p>
        </div>
    </dialog>

    <dialog class="modal" id="checkout-dialog" aria-labelledby="checkout-title">
        <form method="POST" action="{{ route('pharmacy-orders.store') }}" class="modal-form">
            @csrf
            <button type="button" class="modal-close" aria-label="Tutup" data-close-dialog><svg><use href="#i-close"/></svg></button>
            <span class="modal-icon"><svg><use href="#i-bag"/></svg></span>
            <p class="eyebrow">PERMOHONAN PESANAN FARMASI</p>
            <h2 id="checkout-title">Data pemesan</h2>
            <p class="modal-intro">Isi data pemesan untuk mencatat permohonan. Stok, harga, dan metode pembayaran belum terhubung.</p>
            <div data-checkout-items></div>
            <div class="form-grid">
                <label class="field field-full">Nama lengkap<input name="patient_name" autocomplete="name" maxlength="120" required placeholder="Nama pemesan" value="{{ old('patient_name') }}"></label>
                <label class="field field-full">Nomor telepon<input name="phone" type="tel" autocomplete="tel" maxlength="25" required placeholder="Nomor yang dapat dihubungi" value="{{ old('phone') }}"></label>
                <label class="field field-full">Metode pemenuhan<select name="fulfillment" required data-fulfillment><option value="pickup" @selected(old('fulfillment', 'pickup') === 'pickup')>Ambil di farmasi rumah sakit</option><option value="delivery" @selected(old('fulfillment') === 'delivery')>Mohon informasi pengantaran</option></select></label>
                <label class="field field-full is-hidden" data-address-field>Alamat pengantaran<input name="delivery_address" maxlength="500" autocomplete="street-address" placeholder="Alamat lengkap" value="{{ old('delivery_address') }}"></label>
            </div>
            <label class="consent-check"><input type="checkbox" name="privacy_consent" value="1" required><span>Saya menyetujui penggunaan data kontak untuk memproses permohonan ini.</span></label>
            <button class="button button-primary button-submit" type="submit">Ajukan pesanan <svg><use href="#i-arrow"/></svg></button>
            <p class="form-note">Ini bukan pembayaran. Harga dan stok adalah contoh dan harus dikonfirmasi petugas farmasi.</p>
        </form>
    </dialog>

    <div class="toast" role="status" aria-live="polite" data-toast></div>
</body>
</html>
