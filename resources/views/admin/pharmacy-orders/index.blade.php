@extends('layouts.portal')

@section('title', 'Konfirmasi pesanan farmasi')
@section('section', 'Pesanan farmasi')

@section('content')
    <section class="page-heading">
        <div>
            <span class="eyebrow">RUANG FARMASI</span>
            <h1>Konfirmasi pesanan obat</h1>
            <p>Tinjau permohonan produk dan peralatan mandiri yang diajukan pasien.</p>
        </div>
    </section>

    <div class="alert alert-info" role="note">
        <strong>Verifikasi sebelum diproses</strong>
        <span>Stok, harga, resep, pembayaran, dan pengantaran belum terhubung. Pastikan ketersediaan dan kesesuaian produk secara langsung sebelum menghubungi pasien.</span>
    </div>

    <section class="data-panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">PERMOHONAN PASIEN</span>
                <h2>Daftar pesanan</h2>
                <p>Pesanan yang menunggu konfirmasi ditampilkan lebih dahulu.</p>
            </div>
        </div>

        @if ($orders->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">＋</span>
                <strong>Belum ada pesanan farmasi</strong>
                <p>Pesanan mandiri dari pasien akan tampil di bagian ini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>KODE / PASIEN</th>
                            <th>ITEM PESANAN</th>
                            <th>PEMENUHAN</th>
                            <th>ESTIMASI TOTAL</th>
                            <th>STATUS</th>
                            <th>TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <strong class="code-cell">{{ $order->order_code }}</strong><br>
                                    {{ $order->patient_name }}<br>
                                    {{ $order->phone }}
                                    @if ($order->user)
                                        <br>{{ $order->user->email }}
                                    @endif
                                </td>
                                <td>
                                    @foreach ($order->items as $item)
                                        {{ $item['name'] }} × {{ $item['quantity'] }}<br>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $order->fulfillment === 'pickup' ? 'Ambil di farmasi' : 'Pengantaran' }}
                                    @if ($order->delivery_address)
                                        <br>{{ $order->delivery_address }}
                                    @endif
                                </td>
                                <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="status-badge">{{ $order->status === 'menunggu_konfirmasi' ? 'Menunggu konfirmasi' : 'Dikonfirmasi' }}</span>
                                    @if ($order->confirmedBy)
                                        <br>oleh {{ $order->confirmedBy->name }}
                                        @if ($order->confirmed_at)
                                            <br>{{ $order->confirmed_at->format('d M Y H:i') }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($order->status === 'menunggu_konfirmasi')
                                        <form method="POST" action="{{ route('admin.pharmacy-orders.confirm', $order) }}">
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
            {{ $orders->links() }}
        @endif
    </section>
@endsection
