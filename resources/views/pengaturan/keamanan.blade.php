@extends('layouts.app')

@section('header_title', 'Keamanan Sistem')

@section('content')
<div class="space-y-6 w-full max-w-7xl mx-auto pb-12">
    <div>
        <h2 class="text-2xl font-black text-[#1A2E1A]">Pengaturan & Profil</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
        {{-- Memanggil Sidebar Berbagi --}}
        @include('pengaturan.sidebar')

        {{-- Konten Utama Keamanan --}}
        <div class="md:col-span-3 bg-white rounded-[24px] border border-[#D4E8D4] shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#D4E8D4] flex items-center gap-4">
                <div class="w-10 h-10 bg-green-50 text-[#2E7D32] rounded-xl flex items-center justify-center text-base shadow-sm">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1A2E1A] text-sm">Keamanan & Autentikasi Sistem</h3>
                    <p class="text-[11px] text-gray-400">Kelola kebijakan sesi login, proteksi brute-force, dan enkripsi data apotek.</p>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-2xl flex gap-3 items-start">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                    <div class="text-xs text-yellow-800 font-medium">
                        <p class="font-bold mb-0.5">Fitur Keamanan Lanjutan Aktif</p>
                        Sistem saat ini dilindungi oleh middleware <code class="bg-yellow-100 px-1 py-0.5 rounded text-red-600 font-mono">cek.akun.aktif</code> dan pembatasan sesi otomatis berbasis token inventori.
                    </div>
                </div>
                
                {{-- Form/Konten Tambahan Keamanan Bisa Ditaruh Di Sini --}}
                <p class="text-xs text-gray-400 text-center py-8">Konfigurasi enkripsi dan batasan IP dual-layer berjalan otomatis di latar belakang.</p>
            </div>
        </div>
    </div>
</div>
@endsection
