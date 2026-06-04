@extends('layouts.app')

{{-- Mengirimkan judul halaman ke komponen Header bawaan Layout --}}
@section('header_title', 'Dashboard')

@section('content')
{{-- CONTAINER UTAMA: Bersih dari margin/padding ganda, otomatis mengikuti grid bawaan layout --}}
<div class="space-y-6 w-full max-w-7xl mx-auto pb-12">
    
    {{-- Baris Selamat Datang & Pintasan Tombol Cepat --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Penjualan Hari Ini --}}
        <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-sm font-bold border border-emerald-100">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Penjualan Hari Ini</span>
            </div>
            <h3 class="font-sora text-xl font-black text-[#1A2E1A]">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</h3>
            <p class="text-[10px] text-green-600 font-bold mt-1">↑ Berhasil diperbarui</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-sm font-bold border border-blue-100">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Transaksi</span>
            </div>
            <h3 class="font-sora text-xl font-black text-[#1A2E1A]">{{ $totalTransaksi }}</h3>
            <p class="text-[10px] text-blue-600 font-bold mt-1">Hari ini</p>
        </div>

        {{-- Stok Menipis --}}
        <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-sm font-bold border border-amber-100">
                    <i class="fas fa-boxes"></i>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stok Menipis</span>
            </div>
            <h3 class="font-sora text-xl font-black text-[#1A2E1A]">{{ $stokMenipisCount }}</h3>
            <p class="text-[10px] text-orange-600 font-bold mt-1">Perlu restock</p>
        </div>

        {{-- Hampir Kadaluarsa --}}
        <div class="bg-white p-5 rounded-3xl border border-[#D4E8D4] shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-sm font-bold border border-red-100">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hampir Kadaluarsa</span>
            </div>
            <h3 class="font-sora text-xl font-black text-[#1A2E1A]">{{ $hampirKadaluarsaCount }}</h3>
            <p class="text-[10px] text-red-600 font-bold mt-1">Dalam 30 hari</p>
        </div>
    </div>

    {{-- Main Grid (Grafik & List Stok) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Grafik --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-[32px] border border-[#D4E8D4] shadow-sm min-w-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-sora font-bold text-[#1A2E1A]">Grafik Penjualan — 7 Hari Terakhir</h3>
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            </div>
            <div class="h-[300px] w-full relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- List Stok Menipis Samping --}}
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
                        <div class="w-2.5 h-2.5 rounded-full {{ $obat->stok <= 3 ? 'bg-red-500' : 'bg-orange-400' }} shadow"></div>
                        <span class="text-sm font-semibold text-[#1A2E1A]">
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
    <div class="bg-white rounded-[32px] border border-[#D4E8D4] shadow-sm overflow-hidden w-full">
        <div class="p-6 border-b border-[#D4E8D4] flex justify-between items-center">
            <h3 class="font-sora font-bold text-[#1A2E1A]">Transaksi Terbaru</h3>
            <a href="{{ route('transaksi.index') }}" class="px-4 py-1.5 bg-gray-50 text-[10px] font-bold text-gray-500 rounded-lg hover:bg-gray-100 uppercase transition-all">Semua Riwayat</a>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[600px]">
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
                        <td class="px-6 py-4 text-gray-600">{{ $trx->kasir->nama ?? $trx->kasir->name ?? 'Admin' }}</td>
                        <td class="px-6 py-4 font-black text-[#2E7D32]">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded-md text-[9px] font-bold border border-green-100 uppercase">Selesai</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button type="button" onclick="showDetailModal({{ $trx->id }})" class="inline-flex items-center justify-center gap-1.5 px-4 h-8 bg-[#2E7D32] text-white rounded-xl shadow-sm hover:bg-[#1A5E20] transition-all font-sora font-bold text-[10px] uppercase tracking-wider">
                                <i class="fas fa-eye text-xs text-white"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL COMPONENT DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto no-print" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-40" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[32px] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-[#D4E8D4]">
            <div class="px-6 py-5 border-b border-[#D4E8D4] flex justify-between items-center bg-[#F8FAF8]">
                <div>
                    <h3 class="font-sora font-extrabold text-[#1A2E1A] text-base" id="modalNoTrx">#TRX-LOADING...</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5" id="modalWaktu">Memuat...</p>
                </div>
                <button onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-white border border-[#D4E8D4] text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100 text-xs">
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Kasir</p>
                        <p class="font-bold text-gray-700" id="modalKasir">-</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Metode Pembayaran</p>
                        <p class="font-black text-[#2E7D32]" id="modalMetode">-</p>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Daftar Obat Yang Dibeli</p>
                    <div class="border border-[#D4E8D4] rounded-2xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-[9px] font-bold text-gray-400 uppercase border-b border-[#D4E8D4]">
                                <tr>
                                    <th class="px-4 py-2.5">Nama Obat</th>
                                    <th class="px-4 py-2.5 text-center">Qty</th>
                                    <th class="px-4 py-2.5 text-right">Harga</th>
                                    <th class="px-4 py-2.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            {{-- 🔥 PERBAIKAN: Ditambahkan elemen tbody id="modalItemRows" di bawah ini agar data obat bisa disuntikkan oleh Javascript --}}
                            <tbody id="modalItemRows" class="divide-y divide-[#D4E8D4]">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-dashed border-[#D4E8D4] pt-4 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500 font-medium">
                        <span>Uang Diterima</span>
                        <span id="modalUangDiterima">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-500 font-medium">
                        <span>Kembalian</span>
                        <span id="modalKembalian">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <span class="font-sora font-bold text-[#1A2E1A]">Total Bayar</span>
                        <span class="font-sora font-black text-base text-[#2E7D32]" id="modalTotal">Rp 0</span>
                    </div>
                </div>
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

    function showDetailModal(id) {
        const modal = document.getElementById('detailModal');
        document.getElementById('modalNoTrx').innerText = 'Memuat...';
        document.getElementById('modalWaktu').innerText = 'Silahkan tunggu';
        document.getElementById('modalItemRows').innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4 text-gray-400 font-medium animate-pulse">Sedang mengambil data transaksi...</td>
            </tr>
        `;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        fetch(`/transaksi/${id}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success' || res.data) {
                    // Toleransi jika struktur response langsung mengembalikan objek data atau bersarang di res.data
                    const tx = res.data ? res.data : res;
                    const date = new Date(tx.created_at);
                    const tglFormatted = date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                    document.getElementById('modalNoTrx').innerText = `#${tx.no_transaksi}`;
                    document.getElementById('modalWaktu').innerText = tglFormatted;
                    document.getElementById('modalKasir').innerText = tx.kasir ? (tx.kasir.nama || tx.kasir.name || tx.kasir.username) : 'Administrator';
                    document.getElementById('modalMetode').innerText = tx.metode_bayar || tx.metode_pembayaran || '-';

                    let rowsHtml = '';
                    // Atur toleransi jika nama relasi detail transaksi Anda bernama 'details' atau 'detail_transaksi'
                    const detailsArr = tx.details || tx.detail_transaksi || [];
                    
                    detailsArr.forEach(item => {
                        const namaObat = item.nama_obat || (item.obat ? item.obat.nama : 'Obat');
                        const kodeObat = item.kode_obat || (item.obat ? item.obat.kode : '-');
                        const satuanObat = item.satuan || (item.obat ? item.obat.satuan : 'Unit');
                        const hargaSatuan = parseInt(item.harga_satuan || item.harga || 0);
                        const subtotal = parseInt(item.subtotal || (hargaSatuan * item.jumlah) || 0);

                        rowsHtml += `
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3">
                                    <p class="font-bold text-gray-700">${namaObat}</p>
                                    <span class="text-[9px] font-mono bg-gray-100 text-gray-500 px-1 py-0.5 rounded">${kodeObat}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 font-semibold">${item.jumlah} ${satuanObat}</td>
                                <td class="px-4 py-3 text-right text-gray-500">Rp ${hargaSatuan.toLocaleString('id-ID')}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-700">Rp ${subtotal.toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });
                    
                    if(detailsArr.length === 0) {
                        rowsHtml = `<tr><td colspan="4" class="text-center py-4 text-gray-400">Tidak ada detail obat.</td></tr>`;
                    }
                    
                    document.getElementById('modalItemRows').innerHTML = rowsHtml;

                    document.getElementById('modalUangDiterima').innerText = `Rp ${parseInt(tx.uang_diterima || tx.bayar || 0).toLocaleString('id-ID')}`;
                    document.getElementById('modalKembalian').innerText = `Rp ${parseInt(tx.kembalian || 0).toLocaleString('id-ID')}`;
                    document.getElementById('modalTotal').innerText = `Rp ${parseInt(tx.total || 0).toLocaleString('id-ID')}`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('modalNoTrx').innerText = 'Gagal Memuat';
                document.getElementById('modalItemRows').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-red-500 font-bold">Terjadi kesalahan koneksi sistem.</td>
                    </tr>
                `;
            });
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>
@endsection