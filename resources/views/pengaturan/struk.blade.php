@extends('layouts.app')

@section('header_title', 'Format Struk')

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
                    <i class="fas fa-print"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1A2E1A] text-sm">Format Struk Belanja Kasir</h3>
                    <p class="text-[11px] text-gray-400">Sesuaikan header, footer, ukuran kertas, dan logo apotek pada struk fisik.</p>
                </div>
            </div>

            <div class="p-6 text-center py-12 space-y-2">
                <i class="fas fa-sliders-h text-3xl text-gray-300"></i>
                <p class="text-xs font-semibold text-gray-500">Modul Template Thermal Printer 58mm/80mm</p>
                <p class="text-[11px] text-gray-400 max-w-sm mx-auto">Format cetak default menggunakan struk ringkas hemat kertas (Apotek Citra Sehat Standard Layout).</p>
            </div>
        </div>
    </div>
</div>
@endsection