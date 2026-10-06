@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <span class="auth-step">AKSES AKUN PASIEN</span>
    <h2>Selamat datang kembali</h2>
    <p class="auth-lead">Masuk untuk melanjutkan ke layanan kesehatan Anda.</p>

    <form method="POST" action="{{ route('login.store') }}" class="stack-form">
        @csrf
        <label class="field">Alamat email
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus maxlength="255" placeholder="nama@email.com">
        </label>
        <label class="field">Kata sandi
            <input type="password" name="password" autocomplete="current-password" required placeholder="Masukkan kata sandi">
        </label>
        <label class="remember-check"><input type="checkbox" name="remember" value="1"><span>Ingat saya di perangkat ini</span></label>
        <button class="button button-primary button-submit" type="submit">Masuk ke akun <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Buat akun pasien</a></p>
    <p class="auth-help">Lupa kata sandi? Hubungi petugas rumah sakit melalui kontak resmi.</p>
@endsection
