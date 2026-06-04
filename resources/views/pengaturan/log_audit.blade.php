@extends('layouts.app')

@section('header_title', 'Log Audit')

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
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1A2E1A] text-sm">Log Audit Jejak Digital Operator</h3>
                    <p class="text-[11px] text-gray-400">Pantau seluruh riwayat masuk akun, modifikasi harga obat, dan manipulasi stok.</p>
                </div>
            </div>

            <div class="p-6">
                {{-- Preview Log Sederhana --}}
                <div class="border border-gray-100 rounded-2xl overflow-hidden text-[11px]">
                    <div class="bg-gray-50 px-4 py-2.5 font-bold text-gray-500 grid grid-cols-3">
                        <span>WAKTU ACTION</span>
                        <span>OPERATOR</span>
                        <span>DESKRIPSI AKTIVITAS</span>
                    </div>
                    <div class="p-4 text-gray-400 text-center py-8">
                        <i class="fas fa-folder-open mb-1 block text-base text-gray-300"></i>
                        Aktivitas sistem tingkat tinggi terekam langsung di dalam file internal <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600 font-mono">storage/logs/laravel.log</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection