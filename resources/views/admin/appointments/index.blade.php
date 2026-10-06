@extends('layouts.portal')

@section('title', 'Konfirmasi kunjungan')
@section('section', 'Konfirmasi kunjungan')

@section('content')
    <section class="page-heading">
        <div>
            <span class="eyebrow">RUANG PETUGAS</span>
            <h1>Konfirmasi kunjungan</h1>
            <p>Tinjau permohonan pasien dan konfirmasikan kunjungan yang dapat dilayani.</p>
        </div>
    </section>

    <div class="alert alert-info" role="note">
        <strong>Periksa jadwal operasional</strong>
        <span>Konfirmasi di portal ini mencatat persetujuan petugas, tetapi tidak tersambung dengan jadwal atau sistem antrean rumah sakit.</span>
    </div>

    <section class="data-panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">PERMOHONAN PASIEN</span>
                <h2>Daftar kunjungan</h2>
                <p>Permohonan yang menunggu konfirmasi ditampilkan lebih dahulu.</p>
            </div>
        </div>

        @if ($appointments->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">▦</span>
                <strong>Belum ada permohonan kunjungan</strong>
                <p>Permohonan pasien akan tampil di bagian ini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>KODE / PASIEN</th>
                            <th>LAYANAN / POLI</th>
                            <th>JADWAL</th>
                            <th>DOKTER PILIHAN</th>
                            <th>STATUS</th>
                            <th>TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td>
                                    <strong class="code-cell">{{ $appointment->appointment_code }}</strong><br>
                                    {{ $appointment->patient_name }}<br>
                                    {{ $appointment->phone }}
                                    @if ($appointment->user)
                                        <br>{{ $appointment->user->email }}
                                    @endif
                                </td>
                                <td>
                                    {{ ['appointment' => 'Janji temu', 'queue' => 'Antrean', 'consultation' => 'Konsultasi', 'bpjs' => 'BPJS'][$appointment->service_type] ?? 'Kunjungan' }}<br>
                                    {{ $appointment->specialty }}
                                </td>
                                <td>{{ $appointment->visit_date->format('d M Y') }}<br>{{ $appointment->time_slot }}</td>
                                <td>{{ $appointment->doctor ?: 'Tidak ditentukan' }}</td>
                                <td>
                                    <span class="status-badge">{{ $appointment->status === 'menunggu_konfirmasi' ? 'Menunggu konfirmasi' : 'Dikonfirmasi' }}</span>
                                    @if ($appointment->confirmedBy)
                                        <br>oleh {{ $appointment->confirmedBy->name }}
                                        @if ($appointment->confirmed_at)
                                            <br>{{ $appointment->confirmed_at->format('d M Y H:i') }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($appointment->status === 'menunggu_konfirmasi')
                                        <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}">
                                            @csrf
                                            <button class="button button-primary" type="submit">Konfirmasi</button>
                                        </form>
                                    @else
                                        <span>Sudah diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $appointments->links() }}
        @endif
    </section>
@endsection
