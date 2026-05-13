@extends('layouts.app')

@section('header_title', 'Laporan Analitik')

@section('content')
<div class="container mx-auto pb-10">
    {{-- Header Section --}}
    <div class="flex justify-between items-start mb-8 no-print">
        <div>
            <h1 class="text-3xl font-extrabold text-[#1A2E1A] tracking-tight">Laporan Analitik</h1>
            <p class="text-sm text-gray-500 font-medium">Analisa komprehensif data penjualan dan stok apotek — Owner View</p>
        </div>
        <div class="flex gap-3">
            {{-- PDF Export --}}
            <a href="{{ route('laporan.export.pdf', request()->all()) }}" 
               class="px-4 py-2 bg-white border-2 border-[#E8F5E9] rounded-xl text-[11px] font-bold text-[#2E7D32] hover:bg-[#F1F8F1] transition-all uppercase tracking-wider">
               <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </a>
            {{-- Excel Export --}}
            <a href="{{ route('laporan.export.excel', request()->all()) }}" 
               class="px-4 py-2 bg-white border-2 border-[#E8F5E9] rounded-xl text-[11px] font-bold text-[#2E7D32] hover:bg-[#F1F8F1] transition-all uppercase tracking-wider">
               <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
            {{-- Print Button --}}
            <button onclick="window.print()" 
                    class="px-6 py-2 bg-[#2E7D32] rounded-xl text-[11px] font-bold text-white shadow-lg hover:bg-[#1B5E20] transition-all uppercase tracking-wider">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Judul Khusus Saat Print (Hanya Muncul di Kertas) --}}
    <div class="hidden print:block text-center mb-8">
        <h1 class="text-2xl font-bold uppercase">Laporan {{ $tab }} Apotek</h1>
        <p class="text-sm">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        <hr class="mt-4 border-black">
    </div>

    {{-- NAVIGATION TABS --}}
    <div class="flex gap-2 mb-8 no-print">
        @php $tabs = ['penjualan' => 'Laporan Penjualan', 'stok' => 'Laporan Stok', 'profit' => 'Laporan Profit']; @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('laporan.index', ['tab' => $key, 'start' => $startDate, 'end' => $endDate]) }}" 
               class="px-6 py-2 rounded-full text-xs font-bold transition-all {{ $tab == $key ? 'bg-[#2E7D32] text-white shadow-lg' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- STATISTIC CARDS --}}
    @if($tab != 'stok')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Card 1: Revenue/Profit --}}
        <div class="bg-white p-6 rounded-[2.5rem] border border-[#E8F5E9] shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-[#E8F5E9] rounded-xl flex items-center justify-center text-[#2E7D32] no-print">
                    <i class="fas fa-wallet text-sm"></i>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $tab == 'profit' ? 'Total Profit' : 'Total Pendapatan' }}</p>
            </div>
            <h3 class="text-2xl font-black text-[#1A2E1A]">
                Rp {{ number_format($tab == 'profit' ? ($totalProfit ?? 0) : ($totalPendapatan ?? 0), 0, ',', '.') }}
            </h3>
        </div>

        {{-- Card 2: Volume --}}
        <div class="bg-white p-6 rounded-[2.5rem] border border-[#E8F5E9] shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 no-print">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Volume Transaksi</p>
            </div>
            <h3 class="text-2xl font-black text-[#1A2E1A]">{{ $totalTransaksi ?? 0 }}</h3>
        </div>

        {{-- Card 3: Best Seller --}}
        <div class="bg-white p-6 rounded-[2.5rem] border border-[#E8F5E9] shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 no-print">
                    <i class="fas fa-fire text-sm"></i>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Obat Terlaris</p>
            </div>
            <h3 class="text-lg font-black text-[#1A2E1A] truncate">
                {{ $obatTerlaris->nama_obat ?? 'N/A' }}
            </h3>
        </div>

        {{-- Card 4: Average --}}
        <div class="bg-white p-6 rounded-[2.5rem] border border-[#E8F5E9] shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 no-print">
                    <i class="fas fa-hand-holding-usd text-sm"></i>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rata-rata</p>
            </div>
            <h3 class="text-2xl font-black text-[#1A2E1A]">Rp {{ number_format($rataRata ?? 0, 0, ',', '.') }}</h3>
        </div>
    </div>
    @endif

    {{-- MAIN CONTENT AREA --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-[#D4E8D4] overflow-hidden">
        {{-- Filter Header --}}
        <div class="p-6 border-b border-[#D4E8D4] flex flex-wrap items-center justify-between gap-4 bg-gray-50/30 no-print">
            <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative">
                    <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="date" name="start" value="{{ $startDate }}" 
                           class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 outline-none">
                </div>
                <span class="text-gray-400 font-bold text-xs">s/d</span>
                <div class="relative">
                    <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="date" name="end" value="{{ $endDate }}" 
                           class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 outline-none">
                </div>
                <button type="submit" class="bg-[#2E7D32] text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-[#1B5E20] shadow-md">
                    <i class="fas fa-filter mr-2"></i> FILTER
                </button>
            </form>
        </div>

        {{-- TABEL --}}
        @if($tab == 'penjualan' || $tab == 'profit')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-[#D4E8D4]">
                            <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Transaksi</th>
                            <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">No. TRX</th>
                            <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kasir</th>
                            <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Total Tagihan</th>
                            @if($tab == 'profit')
                            <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Estimasi Profit</th>
                            @endif
                            <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($riwayat as $t)
                        <tr class="hover:bg-[#F9FBF9] transition-all">
                            <td class="p-6 text-sm">
                                <span class="font-bold text-gray-800">{{ $t->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-gray-400 block">{{ $t->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="p-6">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg font-mono font-black text-[10px]">
                                    #{{ $t->no_transaksi }}
                                </span>
                            </td>
                            <td class="p-6 font-bold text-gray-700 text-sm">{{ $t->kasir->name ?? 'Admin' }}</td>
                            <td class="p-6 font-black text-[#1A2E1A] text-right">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                            @if($tab == 'profit')
                            <td class="p-6 font-bold text-green-600 text-right">Rp {{ number_format($t->total * 0.2, 0, ',', '.') }}</td>
                            @endif
                            <td class="p-6 text-center no-print">
                                <a href="{{ route('transaksi.show', $t->id) }}" class="text-[10px] font-black text-[#2E7D32]">DETAIL</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400 uppercase text-[10px] font-bold">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 no-print">
                {{ $riwayat->links() }}
            </div>

        @elseif($tab == 'stok')
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="border border-red-100 rounded-3xl p-6 bg-red-50/30">
                    <h4 class="text-red-600 font-black text-xs uppercase mb-4"><i class="fas fa-exclamation-triangle mr-2"></i> Stok Rendah</h4>
                    <ul class="divide-y divide-red-100">
                        @foreach($stokRendah as $s)
                        <li class="py-3 flex justify-between text-sm">
                            <span class="font-bold">{{ $s->nama_obat }}</span>
                            <span class="text-red-600 font-black">{{ $s->stok }} Unit</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-blue-50/30 border border-blue-100 rounded-3xl p-6">
                    <h4 class="text-blue-600 font-black text-xs uppercase mb-4">Info Inventori</h4>
                    <div class="bg-white p-4 rounded-2xl border border-blue-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Total Varian</p>
                        <h4 class="text-2xl font-black text-blue-900">{{ $totalObat }}</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    @media print {
        /* Sembunyikan semua elemen navigasi dan tombol */
        .no-print, nav, aside, footer, button, form, .nav-tabs {
            display: none !important;
        }

        /* Reset layout */
        body { background: white !important; color: black !important; }
        .container { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .bg-white { background: white !important; }
        
        /* Tabel Full Width dengan Border Hitam */
        table { 
            width: 100% !important; 
            border-collapse: collapse !important; 
            margin-top: 20px !important;
        }
        th, td { 
            border: 1px solid #333 !important; 
            padding: 10px !important; 
            font-size: 12px !important;
        }
        th { background-color: #f0f0f0 !important; -webkit-print-color-adjust: exact; }

        /* Sembunyikan Bayangan */
        .shadow-sm, .shadow-lg { shadow: none !important; box-shadow: none !important; }
        
        /* Paksa border muncul */
        .border, .border-b { border-color: #333 !important; }
    }
</style>
@endsection