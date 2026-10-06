<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f7f5">
    <title>@yield('title') · RS Sehat Sentosa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-layout">
        <section class="auth-story">
            <a class="brand auth-brand" href="{{ route('login') }}">
                <span class="brand-mark">+</span>
                <span class="brand-copy"><strong>Sehat<span>Sentosa</span></strong><small>RUMAH SAKIT</small></span>
            </a>
            <div class="auth-story-copy">
                <span class="auth-kicker">PUSAT LAYANAN PASIEN</span>
                <h1>Perjalanan sehat Anda,<br><span>dalam kendali Anda.</span></h1>
                <p>Kelola permohonan kunjungan dan kebutuhan layanan rumah sakit dengan akses akun yang aman dan personal.</p>
                <div class="auth-benefits">
                    <span><i>✓</i> Satu akun untuk seluruh layanan pasien</span>
                    <span><i>✓</i> Riwayat permohonan privat untuk Anda</span>
                    <span><i>✓</i> Informasi pribadi disimpan terenkripsi</span>
                </div>
            </div>
            <span class="auth-story-footer">LAYANAN KESEHATAN, LEBIH DEKAT.</span>
            <span class="auth-art auth-art-one"></span>
            <span class="auth-art auth-art-two"></span>
            <span class="auth-medical-cross">+</span>
        </section>
        <section class="auth-form-panel">
            <div class="auth-mobile-brand">
                <span class="brand-mark">+</span><strong>Sehat<span>Sentosa</span></strong>
            </div>
            <div class="auth-card">
                @if ($errors->any())
                    <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
            <p class="auth-legal">Dengan melanjutkan, Anda menyetujui pemrosesan data yang diperlukan untuk layanan rumah sakit.</p>
        </section>
    </main>
</body>
</html>
