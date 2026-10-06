<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f7f5">
    <meta name="description" content="@yield('description', 'Portal layanan pasien RS Sehat Sentosa.')">
    <title>@yield('title', 'Portal Pasien') · RS Sehat Sentosa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}" aria-label="RS Sehat Sentosa, beranda">
                <span class="brand-mark">+</span>
                <span class="brand-copy"><strong>sehat<span>sentosa</span></strong><small>{{ auth()->user()->isPatient() ? 'PORTAL PASIEN' : 'PORTAL PETUGAS' }}</small></span>
            </a>
            <span class="side-label">{{ auth()->user()->isPatient() ? 'RUANG PASIEN' : 'RUANG PETUGAS' }}</span>
            <nav class="side-nav">
                @if (auth()->user()->isPatient())
                <a class="nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}" aria-label="Ringkasan" title="Ringkasan"><span class="nav-icon">⌂</span><span class="nav-label">Ringkasan</span></a>
                <a class="nav-link {{ request()->routeIs('appointments.*') ? 'is-active' : '' }}" href="{{ route('appointments.create') }}" aria-label="Janji temu" title="Janji temu"><span class="nav-icon">▦</span><span class="nav-label">Janji temu</span></a>
                <a class="nav-link {{ request()->routeIs('queue.*') ? 'is-active' : '' }}" href="{{ route('queue.create') }}" aria-label="Antrean" title="Antrean"><span class="nav-icon">▤</span><span class="nav-label">Antrean</span></a>
                <a class="nav-link {{ request()->routeIs('consultations.*') ? 'is-active' : '' }}" href="{{ route('consultations.create') }}" aria-label="Konsultasi" title="Konsultasi"><span class="nav-icon">◌</span><span class="nav-label">Konsultasi</span></a>
                <a class="nav-link {{ request()->routeIs('bpjs.*') ? 'is-active' : '' }}" href="{{ route('bpjs.create') }}" aria-label="BPJS dan asuransi" title="BPJS dan asuransi"><span class="nav-icon">▣</span><span class="nav-label">BPJS & asuransi</span></a>
                <a class="nav-link {{ request()->routeIs('pharmacy*') ? 'is-active' : '' }}" href="{{ route('pharmacy.index') }}" aria-label="Farmasi" title="Farmasi"><span class="nav-icon">＋</span><span class="nav-label">Farmasi</span></a>
                @else
                    @can('manage-appointments')
                        <a class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'is-active' : '' }}" href="{{ route('admin.appointments.index') }}" aria-label="Konfirmasi kunjungan" title="Konfirmasi kunjungan"><span class="nav-icon">▦</span><span class="nav-label">Konfirmasi kunjungan</span></a>
                    @endcan
                    @can('manage-pharmacy-orders')
                        <a class="nav-link {{ request()->routeIs('admin.pharmacy-orders.*') ? 'is-active' : '' }}" href="{{ route('admin.pharmacy-orders.index') }}" aria-label="Pesanan farmasi" title="Pesanan farmasi"><span class="nav-icon">＋</span><span class="nav-label">Pesanan farmasi</span></a>
                    @endcan
                @endif
            </nav>
            <span class="side-label side-label-lower">AKUN</span>
            <nav class="side-nav">
                <a class="nav-link {{ request()->routeIs('account.*') ? 'is-active' : '' }}" href="{{ route('account.show') }}" aria-label="Profil saya" title="Profil saya"><span class="nav-icon">◎</span><span class="nav-label">Profil saya</span></a>
            </nav>
            <div class="sidebar-bottom">
                <div class="privacy-card">
                    <span class="privacy-icon">✓</span>
                    <strong>Data Anda terlindungi</strong>
                    <p>Informasi akun disimpan secara terenkripsi dan dibatasi untuk kebutuhan layanan.</p>
                </div>
                <div class="profile-mini">
                    <span class="avatar avatar-small">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="profile-identity"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role->label() }}</small></span>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button class="logout-button" type="submit" aria-label="Keluar dari akun" title="Keluar">↗</button>
                    </form>
                </div>
            </div>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <div class="breadcrumbs"><span>{{ auth()->user()->isPatient() ? 'Ruang pasien' : 'Ruang petugas' }}</span><span class="crumb-slash">/</span><strong>@yield('section', 'Ringkasan')</strong></div>
                <div class="topbar-actions">
                    <span class="today-label">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                    <a class="avatar avatar-top" href="{{ route('account.show') }}" aria-label="Lihat profil">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</a>
                </div>
            </header>
            <div class="page-wrap">
                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error" role="alert">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
                @endif
                @yield('content')
                <footer class="page-footer">
                    <span>© {{ now()->year }} RS Sehat Sentosa · Portal tahap awal</span>
                    <span>Jadwal, BPJS, farmasi, dan pembayaran perlu konfirmasi resmi.</span>
                </footer>
            </div>
        </main>
    </div>
    <nav class="mobile-nav" aria-label="Navigasi cepat">
        @if (auth()->user()->isPatient())
        <a href="{{ route('home') }}">⌂<span>Beranda</span></a>
        <a href="{{ route('appointments.create') }}">▦<span>Janji</span></a>
        <a href="{{ route('queue.create') }}">▤<span>Antrean</span></a>
        <a href="{{ route('pharmacy.index') }}">＋<span>Farmasi</span></a>
        <a href="{{ route('account.show') }}">◎<span>Akun</span></a>
        @else
            @can('manage-appointments')
                <a href="{{ route('admin.appointments.index') }}">▦<span>Kunjungan</span></a>
            @endcan
            @can('manage-pharmacy-orders')
                <a href="{{ route('admin.pharmacy-orders.index') }}">＋<span>Farmasi</span></a>
            @endcan
            <a href="{{ route('account.show') }}">◎<span>Akun</span></a>
        @endif
    </nav>
</body>
</html>
