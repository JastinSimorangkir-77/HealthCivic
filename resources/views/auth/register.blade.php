@extends('layouts.auth')

@section('title', 'Buat Akun')

@section('content')
    <span class="auth-step">REGISTRASI PASIEN</span>
    <h2>Buat akun pasien</h2>
    <p class="auth-lead">Daftarkan akun untuk menyimpan permohonan layanan Anda secara privat.</p>

    <form method="POST" action="{{ route('register.store') }}" class="stack-form">
        @csrf
        <label class="field">Nama lengkap
            <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus maxlength="120" placeholder="Sesuai kartu identitas">
        </label>
        <label class="field">Alamat email
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="255" placeholder="nama@email.com">
        </label>
        <label class="field">Nomor telepon
            <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" required maxlength="25" placeholder="Contoh: 0812 3456 7890">
        </label>
        <label class="field">Kata sandi
            <input type="password" name="password" autocomplete="new-password" required minlength="12" placeholder="Minimal 12 karakter, huruf dan angka">
        </label>
        <label class="field">Konfirmasi kata sandi
            <input type="password" name="password_confirmation" autocomplete="new-password" required minlength="12" placeholder="Ketik ulang kata sandi">
        </label>
        <label class="remember-check consent-check"><input type="checkbox" name="privacy_consent" value="1" required><span>Saya menyetujui pemrosesan data akun untuk menggunakan layanan pasien.</span></label>
        <button class="button button-primary button-submit" type="submit">Buat akun dengan aman <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    <p class="auth-help">Nomor telepon serta data profil disimpan dalam bentuk terenkripsi.</p>
@endsection
