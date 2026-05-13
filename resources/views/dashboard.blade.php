@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAF8] pb-24">
    {{-- Top Navbar --}}
    <div class="bg-white px-6 py-4 border-b border-[#D4E8D4] flex justify-between items-center sticky top-0 z-40 no-print">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#2E7D32] rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-plus text-white"></i>
            </div>
            <h1 class="font-sora font-extrabold text-[#1A2E1A] text-lg leading-none tracking-tight uppercase">Dashboard</h1>
        </div>
        <div class="flex items-center gap-3">
            <button class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 relative hover:bg-gray-100 transition-colors">
                <i class="fas fa-bell"></i>
                @if($stokMenipisCount > 0 || $hampirKadaluarsaCount > 0)
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                @endif
            </button>
            <div class="w-10 h-10 bg-[#E8F5E9] rounded-full flex items-center justify-center text-[#2E7D32] font-bold border border-[#D4E8D4]">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6 max-w-7xl mx-auto">
        {{-- Welcome Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-[#1A2E1A]">Ringkasan Operasional</h2>
                <p class="text-sm text-gray-500">Selamat datang kembali! Berikut data Apotek Citra Sehat hari ini.</p>
            </div>
            <div class="flex flex-wrap gap-2 no-print">
                <a href="{{ route('transaksi.create') }}" class="px-4 py-2 bg-[#2E7D32] text-white rounded-xl font-bold text-xs shadow-md hover:bg-[#1A5E20] transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Transaksi Baru
                </a>
                <a href="{{ route('obat.create') }}" class="px-4 py-2 bg-white border border-[#D4E8D4] text-[#1A2E1A] rounded-xl font-bold text-xs hover:bg-gray-100 transition-all flex items-center gap-2">
                    <i class="fas fa-pills text-[#2E7D32]"></i> Tambah Obat
                </a>
            </div>
        </div>

        {{-- Statistik Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Penjualan --}}
            <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Penjualan Hari Ini</span>
                </div>
                <h3 class="font-sora text-xl font-black text-[#1A2E1A]">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-green-600 font-bold mt-1">↑ Berhasil diperbarui</p>
            </div>

            {{-- Transaksi --}}
            <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Transaksi</span>
                </div>
                <h3 class="font-sora text-xl font-black text-[#1A2E1A]">{{ $totalTransaksi }}</h3>
                <p class="text-[10px] text-blue-600 font-bold mt-1">Hari ini</p>
            </div>

            {{-- Stok Menipis Count --}}
            <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stok Menipis</span>
                </div>
                <h3 class="font-sora text-xl font-black text-[#1A2E1A]">{{ $stokMenipisCount }}</h3>
                <p class="text-[10px] text-orange-600 font-bold mt-1">Perlu restock</p>
            </div>

            {{-- Kadaluarsa Count --}}
            <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hampir Kadaluarsa</span>
                </div>
                <h3 class="font-sora text-xl font-black text-[#1A2E1A]">{{ $hampirKadaluarsaCount }}</h3>
                <p class="text-[10px] text-red-600 font-bold mt-1">Dalam 30 hari</p>
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Grafik --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-[32px] border border-[#D4E8D4] shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-sora font-bold text-[#1A2E1A]">Grafik Penjualan — 7 Hari Terakhir</h3>
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                </div>
                <div class="h-[300px] w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- List Stok Menipis (REFIXED) --}}
            <div class="bg-white rounded-[24px] border border-[#D4E8D4] shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-[#D4E8D4] flex justify-between items-center bg-white">
                    <h3 class="font-sora font-bold text-[#1A2E1A] text-base">Stok Menipis</h3>
                    <a href="{{ route('obat.index') }}" class="text-[11px] font-bold text-[#2E7D32] hover:underline uppercase tracking-wider flex items-center gap-1">
                        Lihat Semua <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-[#D4E8D4] flex-1">
                    @forelse($listStokMenipis as $obat)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-[#F8FAF8] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full {{ $obat->stok <= 3 ? 'bg-red-500' : 'bg-orange-400' }}"></div>
                            <span class="text-sm font-semibold text-[#1A2E1A]">
                                {{-- Solusi Nama: Mencoba nama_obat atau nama --}}
                                {{ $obat->nama_obat ?? $obat->nama ?? 'Obat Tanpa Nama' }}
                            </span>
                        </div>
                        <span class="text-[11px] font-bold {{ $obat->stok <= 3 ? 'text-red-500' : 'text-orange-500' }} uppercase">
                            Stok: {{ $obat->stok }}
                        </span>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                        <i class="fas fa-check-circle text-green-200 text-4xl mb-3"></i>
                        <p class="text-xs text-gray-400 font-medium">Stok masih aman terjaga.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tabel Transaksi --}}
        <div class="bg-white rounded-[32px] border border-[#D4E8D4] shadow-sm overflow-hidden">
            <div class="p-6 border-b border-[#D4E8D4] flex justify-between items-center">
                <h3 class="font-sora font-bold text-[#1A2E1A]">Transaksi Terbaru</h3>
                <a href="{{ route('transaksi.index') }}" class="px-4 py-1.5 bg-gray-50 text-[10px] font-bold text-gray-500 rounded-lg hover:bg-gray-100 uppercase transition-all">Semua Riwayat</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4">No. Transaksi</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Kasir</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($transaksiTerbaru as $trx)
                        <tr class="text-xs hover:bg-gray-50/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-700">#{{ $trx->no_transaksi }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $trx->kasir->name ?? 'Admin' }}</td>
                            <td class="px-6 py-4 font-black text-[#2E7D32]">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-green-50 text-green-600 rounded-md text-[9px] font-bold border border-green-100 uppercase">Selesai</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('transaksi.show', $trx->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-[#D4E8D4] rounded-lg text-gray-400 hover:text-[#2E7D32] transition-all">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = @json($grafikData->pluck('label'));
        const totals = @json($grafikData->pluck('total'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: totals,
                    backgroundColor: 'rgba(46, 125, 50, 0.1)',
                    borderColor: '#2E7D32',
                    borderWidth: 2,
                    borderRadius: 12,
                    hoverBackgroundColor: '#2E7D32'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: '#F0F4F0' },
                        ticks: {
                            callback: (val) => val >= 1000 ? (val/1000) + ' Rb' : val
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection