@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 lg:p-6 bg-gray-50 min-h-screen">
    <div class="mb-6">
        <nav class="text-sm text-gray-500 mb-2">Penjualan > <span class="text-green-600 font-semibold">Riwayat Transaksi</span></nav>
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h1>
    </div>

    {{-- Form Filter --}}
    <form action="{{ route('transaksi.index') }}" method="GET" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex items-center gap-2">
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Dari</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none border-gray-200">
                </div>
                <span class="text-gray-400 mt-4">s/d</span>
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Sampai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none border-gray-200">
                </div>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Kasir</label>
                <select name="kasir_id" class="border rounded-lg p-2 text-sm min-w-[140px] outline-none border-gray-200">
                    <option value="">Semua Kasir</option>
                    @foreach($kasirList as $k)
                        <option value="{{ $k->id }}" {{ request('kasir_id') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Metode</label>
                <select name="metode" class="border rounded-lg p-2 text-sm min-w-[140px] outline-none border-gray-200">
                    <option value="">Semua Metode</option>
                    <option value="Tunai" {{ request('metode') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ request('metode') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="Debit" {{ request('metode') == 'Debit' ? 'selected' : '' }}>Debit</option>
                    <option value="E-Wallet" {{ request('metode') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
            </div>

            <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-800 transition shadow-md shadow-green-100">
                Filter
            </button>
            
            <div class="ml-auto flex gap-2">
                {{-- Tombol PDF --}}
                <a href="{{ route('transaksi.export.pdf', request()->query()) }}" 
                   class="flex items-center gap-2 border border-red-200 text-red-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Export PDF
                </a>

                {{-- Tombol Excel --}}
                <a href="{{ route('transaksi.export.excel', request()->query()) }}" 
                   class="flex items-center gap-2 border border-green-200 text-green-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </a>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">No. Transaksi</th>
                        <th class="p-4 font-semibold">Tanggal & Waktu</th>
                        <th class="p-4 font-semibold">Kasir</th>
                        <th class="p-4 font-semibold">Total</th>
                        <th class="p-4 font-semibold">Metode</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($riwayat as $trx)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-4 font-bold text-gray-700">{{ $trx->no_transaksi }}</td>
                        <td class="p-4 text-gray-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-4 text-gray-600">{{ $trx->kasir->name ?? 'Admin' }}</td>
                        <td class="p-4 font-bold text-gray-800">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td class="p-4">
                            <span class="text-xs px-2 py-1 bg-gray-100 rounded text-gray-600">{{ $trx->metode_bayar }}</span>
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase 
                                {{ $trx->status == 'selesai' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $trx->status }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                <button onclick="showDetail({{ $trx->id }})" class="bg-white border border-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50 hover:border-green-500 transition">Detail</button>
                                <button onclick="cetakStruk({{ $trx->id }})" class="bg-white border border-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50 hover:border-blue-500 transition">Struk</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-gray-400 italic">Data transaksi tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 bg-white border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="text-xs text-gray-500 font-medium">
                Menampilkan <b>{{ $riwayat->firstItem() ?? 0 }}</b> sampai <b>{{ $riwayat->lastItem() ?? 0 }}</b> dari <b>{{ $riwayat->total() }}</b> transaksi
            </span>
            <div class="custom-pagination">
                {!! $riwayat->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="modalDetail" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 transition-all">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] shadow-2xl overflow-hidden flex flex-col scale-95 transition-transform duration-300" id="modalContainer">
        <div class="p-6 border-b flex justify-between items-center bg-white">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Detail Transaksi</h3>
                <p class="text-xs text-gray-500" id="modalTrxId">ID Transaksi: -</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="modalContent" class="p-6 overflow-y-auto bg-gray-50/30">
            {{-- Konten diisi via JavaScript --}}
        </div>
        <div class="p-4 border-t bg-white flex justify-end gap-3">
            <button onclick="closeModal()" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-xl font-bold hover:bg-gray-200 transition">Tutup</button>
            <button id="btnCetakModal" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100">Cetak Struk</button>
        </div>
    </div>
</div>

<script>
    const formatRupiah = (num) => new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR', 
        minimumFractionDigits: 0 
    }).format(num);

    async function showDetail(id) {
        const modal = document.getElementById('modalDetail');
        const container = document.getElementById('modalContainer');
        const content = document.getElementById('modalContent');
        const btnCetak = document.getElementById('btnCetakModal');
        
        modal.classList.remove('hidden');
        setTimeout(() => container.classList.remove('scale-95'), 10);
        content.innerHTML = '<div class="flex justify-center py-20"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div></div>';

        try {
            const response = await fetch(`/transaksi/${id}`);
            const res = await response.json();

            if (res.status === 'success') {
                const trx = res.data;
                document.getElementById('modalTrxId').innerText = `ID: ${trx.no_transaksi}`;
                btnCetak.setAttribute('onclick', `cetakStruk(${trx.id})`);

                let itemsHtml = trx.details.map(item => `
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-2">
                            <p class="font-semibold text-gray-800 text-sm">${item.nama_obat}</p>
                            <p class="text-[10px] text-gray-400">Harga Satuan: ${formatRupiah(item.harga_satuan)}</p>
                        </td>
                        <td class="py-3 text-center text-gray-600 font-medium">${item.jumlah}</td>
                        <td class="py-3 text-right font-bold text-gray-800">${formatRupiah(item.subtotal)}</td>
                    </tr>
                `).join('');

                content.innerHTML = `
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm mb-6 grid grid-cols-2 gap-y-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Waktu Transaksi</p>
                            <p class="text-sm font-semibold text-gray-700">${new Date(trx.created_at).toLocaleString('id-ID')}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Metode Pembayaran</p>
                            <p class="text-sm font-bold text-green-600">${trx.metode_bayar}</p> 
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Kasir</p>
                            <p class="text-sm font-semibold text-gray-700">${trx.kasir ? trx.kasir.name : 'Admin'}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Status</p>
                            <span class="text-[10px] px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-bold uppercase">${trx.status}</span>
                        </div>
                    </div>

                    <table class="w-full mb-6">
                        <thead>
                            <tr class="text-[10px] text-gray-400 uppercase font-black border-b">
                                <th class="pb-2 text-left">Produk</th>
                                <th class="pb-2 text-center">Qty</th>
                                <th class="pb-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>

                    <div class="bg-gray-50 p-4 rounded-xl space-y-2">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Subtotal</span>
                            <span>${formatRupiah(trx.subtotal)}</span>
                        </div>
                        <div class="flex justify-between text-base font-black pt-2 border-t border-gray-200">
                            <span class="text-gray-800">TOTAL</span>
                            <span class="text-green-600 text-xl">${formatRupiah(trx.total)}</span>
                        </div>
                        ${trx.metode_bayar === 'Tunai' ? `
                            <div class="flex justify-between text-xs text-gray-500 pt-2">
                                <span>Uang Diterima</span>
                                <span>${formatRupiah(trx.uang_diterima)}</span>
                            </div>
                            <div class="flex justify-between text-xs text-orange-600 font-bold">
                                <span>Kembalian</span>
                                <span>${formatRupiah(trx.kembalian)}</span>
                            </div>
                        ` : ''}
                    </div>
                `;
            } else {
                throw new Error(res.message || 'Gagal mengambil data');
            }
        } catch (error) {
            console.error(error);
            content.innerHTML = `<div class="text-center py-10"><p class="text-red-500 font-bold text-sm">Gagal memuat data.</p><p class="text-xs text-gray-400">${error.message}</p></div>`;
        }
    }

    function closeModal() {
        const modal = document.getElementById('modalDetail');
        const container = document.getElementById('modalContainer');
        container.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 100);
    }

    function cetakStruk(id) {
        window.open(`/transaksi/${id}/print`, '_blank');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalDetail');
        if (event.target == modal) closeModal();
    }
</script>

<style>
    .custom-pagination nav { display: flex; gap: 5px; }
    .custom-pagination nav span, .custom-pagination nav a {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        background: white;
        border: 1px solid #e5e7eb;
    }
    .custom-pagination .active { background: #15803d !important; color: white !important; border-color: #15803d !important; }
    .custom-pagination svg { width: 16px; height: 16px; display: inline; }
</style>
@endsection