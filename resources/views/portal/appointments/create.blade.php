@extends('layouts.portal')

@section('title', $service['title'])
@section('section', $service['title'])

@section('content')
    <section class="page-heading">
        <div><span class="eyebrow">LAYANAN PASIEN</span><h1>{{ $service['title'] }}</h1><p>{{ $service['description'] }}</p></div>
        <a class="button button-secondary" href="{{ route('home') }}">← Kembali ke ringkasan</a>
    </section>
    <div class="alert alert-info" role="note"><strong>Informasi penting</strong><span>Kode yang diterbitkan adalah referensi permohonan, bukan konfirmasi jadwal atau nomor antrean. Jangan gunakan formulir ini untuk keadaan darurat.</span></div>

    <section class="form-panel">
        <div class="form-panel-heading"><span class="form-panel-icon">▦</span><div><h2>Detail permohonan</h2><p>Informasi akun Anda digunakan untuk menghubungi terkait permohonan ini.</p></div></div>
        <div class="account-summary"><span>Atas nama</span><strong>{{ auth()->user()->name }}</strong><span>Kontak terenkripsi</span><strong>{{ auth()->user()->email }}</strong></div>
        <form method="POST" action="{{ route('appointments.store') }}" class="stack-form">
            @csrf
            <input type="hidden" name="service_type" value="{{ $serviceType }}">
            <div class="form-grid">
                <label class="field">Poli tujuan
                    <select name="specialty" required><option value="">Pilih poli</option>@foreach ($specialties as $value => $label)<option value="{{ $value }}" @selected(old('specialty') === $value)>{{ $label }}</option>@endforeach</select>
                </label>
                <label class="field">Dokter pilihan <span class="field-optional">(opsional)</span>
                    <input name="doctor" value="{{ old('doctor') }}" maxlength="120" placeholder="Nama dokter bila tersedia">
                </label>
                <label class="field">Tanggal kunjungan
                    <input type="date" name="visit_date" value="{{ old('visit_date') }}" min="{{ today()->toDateString() }}" max="{{ today()->addDays(90)->toDateString() }}" required>
                </label>
                <label class="field">Waktu yang diinginkan
                    <select name="time_slot" required><option value="">Pilih waktu</option>@foreach ($timeSlots as $slot)<option value="{{ $slot }}" @selected(old('time_slot') === $slot)>{{ str_replace('–', ' – ', $slot) }}</option>@endforeach</select>
                </label>
                @if ($serviceType === 'bpjs')
                    <div class="field field-full"><span>Metode pembayaran</span><input value="BPJS Kesehatan" readonly><input type="hidden" name="insurance" value="bpjs"></div>
                @else
                    <label class="field field-full">Metode pembayaran
                        <select name="insurance" required><option value="">Pilih metode</option><option value="umum" @selected(old('insurance') === 'umum')>Umum / mandiri</option><option value="bpjs" @selected(old('insurance') === 'bpjs')>BPJS Kesehatan</option><option value="asuransi" @selected(old('insurance') === 'asuransi')>Asuransi swasta</option></select>
                    </label>
                @endif
                @if ($serviceType === 'bpjs' || old('insurance') === 'bpjs')
                    <label class="field field-full">Nomor peserta BPJS
                        <input name="bpjs_number" value="{{ old('bpjs_number') }}" maxlength="30" autocomplete="off" required placeholder="Nomor peserta BPJS">
                    </label>
                @else
                    <label class="field field-full">Nomor peserta BPJS <span class="field-optional">(jika memilih BPJS)</span>
                        <input name="bpjs_number" value="{{ old('bpjs_number') }}" maxlength="30" autocomplete="off" placeholder="Isi bila menggunakan BPJS">
                    </label>
                @endif
                @if ($serviceType === 'consultation')
                    <label class="field field-full">Catatan untuk permohonan konsultasi <span class="field-optional">(hindari informasi darurat)</span>
                        <textarea name="notes" rows="4" maxlength="2000" placeholder="Jelaskan singkat hal yang ingin dikonsultasikan">{{ old('notes') }}</textarea>
                    </label>
                @endif
            </div>
            <label class="consent-check"><input type="checkbox" name="privacy_consent" value="1" required><span>Saya menyetujui penggunaan data akun untuk memproses permohonan ini. Catatan konsultasi disimpan terenkripsi.</span></label>
            <button class="button button-primary button-submit" type="submit">Kirim permohonan <span aria-hidden="true">→</span></button>
        </form>
    </section>
@endsection
