@extends('layouts.app')

@section('header_title', 'Backup & Restore')

@section('content')
<div class="space-y-6 w-full max-w-7xl mx-auto pb-12">
    <div>
        <h2 class="text-2xl font-black text-[#1A2E1A]">Pengaturan & Profil</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
        @include('pengaturan.sidebar')

        <div class="md:col-span-3 bg-white rounded-[24px] border border-[#D4E8D4] shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#D4E8D4] flex items-center gap-4">
                <div class="w-10 h-10 bg-green-50 text-[#2E7D32] rounded-xl flex items-center justify-center text-base shadow-sm">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1A2E1A] text-sm">Cadangan & Pemulihan Basis Data</h3>
                    <p class="text-[11px] text-gray-400">Unduh salinan database SQL instan untuk mencegah kehilangan data transaksi.</p>
                </div>
            </div>

            <div class="p-6 text-center py-12 space-y-3">
                <i class="fas fa-database text-3xl text-gray-300"></i>
                <p class="text-xs font-semibold text-gray-500">Penyimpanan Terintegrasi (Local Storage Assets)</p>
                <p class="text-[11px] text-gray-400 max-w-md mx-auto mb-4">Disarankan melakukan backup manual setiap akhir bulan sebelum mencetak rekapan Laporan Analitik Keuntungan.</p>
                <button class="px-4 py-2 bg-[#2E7D32] text-white font-bold text-xs rounded-xl shadow-sm hover:bg-[#1A5E20] transition-all">
                    <i class="fas fa-download mr-1"></i> Jalankan Ekspor SQL (.sql)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection