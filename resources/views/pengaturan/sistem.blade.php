@extends('layouts.app')

@section('header_title', 'Pengaturan Sistem')

@section('content')
<div class="space-y-6 w-full max-w-7xl mx-auto pb-12">
    <div>
        <h2 class="text-2xl font-black text-[#1A2E1A]">Pengaturan & Profil</h2>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-100 font-semibold flex items-center gap-2">
        <i class="fas fa-check-circle text-green-600"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 mb-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-100 font-semibold">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <span>Terjadi kesalahan validasi:</span>
        </div>
        <ul class="list-disc list-inside pl-2 text-xs font-normal space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
        {{-- 🌟 PEMANGGILAN KOMPONEN SIDEBAR UTAMA --}}
        @include('pengaturan.sidebar')

        {{-- Form Sistem --}}
        <div class="md:col-span-3 bg-white rounded-[24px] border border-[#D4E8D4] shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#D4E8D4] flex items-center gap-4">
                <div class="w-10 h-10 bg-green-50 text-[#2E7D32] rounded-xl flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1A2E1A] text-sm">Konfigurasi Apotek Citra Sehat</h3>
                    <p class="text-[11px] text-gray-400">Kelola informasi instansi dan ambang batas peringatan sistem secara berkala.</p>
                </div>
            </div>

            <form action="{{ route('pengaturan.sistem.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Nama Apotek</label>
                        <input type="text" value="{{ $pengaturan->nama_apotek ?? 'Apotek Citra Sehat' }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-400 cursor-not-allowed select-none focus:outline-none" readonly>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Lokasi / Unit</label>
                        <input type="text" value="{{ $pengaturan->lokasi_unit ?? 'IPB Dramaga Campus' }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-gray-400 cursor-not-allowed select-none focus:outline-none" readonly>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Batas Minimum Stok Kritis</label>
                        <div class="relative flex items-center">
                            <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $pengaturan->stok_minimum ?? 10) }}" class="w-full px-4 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all">
                            <span class="absolute right-4 text-[10px] font-black text-gray-400 uppercase">Unit</span>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Pengingat Kadaluarsa (Maksimal)</label>
                        <div class="relative flex items-center">
                            <input type="number" name="hari_kadaluarsa" value="{{ old('hari_kadaluarsa', $pengaturan->hari_kadaluarsa ?? 30) }}" class="w-full px-4 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all">
                            <span class="absolute right-4 text-[10px] font-black text-gray-400 uppercase">Hari</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 rounded-xl font-bold text-xs transition-all">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-[#2E7D32] hover:bg-[#1A5E20] text-white rounded-xl font-bold text-xs shadow-sm transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection