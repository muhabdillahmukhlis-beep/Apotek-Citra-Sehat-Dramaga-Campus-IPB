@extends('layouts.app')

@section('header_title', 'Notifikasi Sistem')

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
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1A2E1A] text-sm">Kebijakan Alert & Notifikasi Otomatis</h3>
                    <p class="text-[11px] text-gray-400">Atur kanal distribusi peringatan obat expired dan broadcast dasbor kasir.</p>
                </div>
            </div>

            <div class="p-6 text-center py-12 text-xs font-semibold text-gray-500">
                <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2 block"></i>
                Notifikasi otomatis terhubung langsung dengan ambang hari menu <a href="{{ route('pengaturan.sistem.index') }}" class="text-[#2E7D32] underline">Info Apotek</a>.
            </div>
        </div>
    </div>
</div>
@endsection