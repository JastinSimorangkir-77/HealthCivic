@extends('layouts.portal')

@section('title', 'Profil saya')
@section('section', 'Profil saya')

@section('content')
    <section class="page-heading">
        <div><span class="eyebrow">PENGATURAN AKUN</span><h1>Profil pasien</h1><p>Informasi akun yang digunakan untuk permohonan layanan Anda.</p></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Keluar dari akun ↗</button></form>
    </section>
    <section class="form-panel profile-panel">
        <div class="profile-header"><span class="avatar profile-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><div><h2>{{ $user->name }}</h2><p>Pasien portal · Terdaftar {{ $user->created_at->format('d M Y') }}</p></div></div>
        <dl class="profile-details">
            <div><dt>Nama lengkap</dt><dd>{{ $user->name }}</dd></div>
            <div><dt>Alamat email</dt><dd>{{ $user->email }}</dd></div>
            <div><dt>Nomor telepon</dt><dd>{{ $user->phone }}</dd></div>
            <div><dt>Privasi</dt><dd><span class="status-badge">Data profil terenkripsi</span></dd></div>
        </dl>
        <div class="alert alert-info"><strong>Perubahan data akun</strong><span>Untuk memperbarui data, hubungi petugas melalui kanal resmi rumah sakit. Pengaturan profil mandiri belum tersedia.</span></div>
    </section>
@endsection
