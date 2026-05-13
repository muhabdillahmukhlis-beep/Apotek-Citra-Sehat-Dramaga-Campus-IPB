@extends('layouts.app')

@section('header_title', 'Monitoring Kadaluarsa')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-[#D4E8D4] overflow-hidden text-black">
    {{-- Header Section --}}
    <div class="p-8 border-b border-[#D4E8D4] flex justify-between items-center bg-gradient-to-r from-white to-[#F0F4F0]">
        <div>
            <h3 class="text-xl font-bold text-gray-800 font-sora uppercase tracking-tight">Monitoring Obat Kadaluarsa</h3>
            <p class="text-sm text-gray-500 mt-1 font-medium">Daftar obat yang mendekati masa kadaluarsa (Filter: {{ $days }} hari ke depan)</p>
        </div>
        
        <form action="{{ route('obat.expired') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-bold text-gray-400 uppercase">Rentang Waktu:</label>
            <select name="days" onchange="this.form.submit()" class="bg-white border border-[#D4E8D4] text-sm font-bold text-[#1B5E20] rounded-xl px-4 py-2 focus:ring-2 focus:ring-[#2E7D32] outline-none shadow-sm cursor-pointer">
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari</option>
                <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 Hari</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari</option>
            </select>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#F8FAF8] text-[#1B5E20] font-sora text-[11px] uppercase tracking-widest border-b border-[#D4E8D4]">
                    <th class="px-8 py-5 font-bold">Nama Obat</th>
                    <th class="px-8 py-5 font-bold">Kategori</th>
                    <th class="px-8 py-5 font-bold text-center">Tgl Kadaluarsa</th>
                    <th class="px-8 py-5 font-bold text-center">Status Sisa Hari</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#D4E8D4]">
                @php $today = \Carbon\Carbon::now()->startOfDay(); @endphp
                
                @forelse($obats as $obat)
                @php
                    $tglKadaluarsa = \Carbon\Carbon::parse($obat->tgl_kadaluarsa)->startOfDay();
                    $diff = $today->diffInDays($tglKadaluarsa, false);
                @endphp
                <tr class="hover:bg-[#F0F4F0]/50 transition-colors group">
                    {{-- Nama Obat --}}
                    <td class="px-8 py-5">
                        <span class="font-bold text-gray-800 text-sm group-hover:text-[#2E7D32]">{{ $obat->nama }}</span>
                    </td>

                    {{-- Kategori (Perbaikan Properti Nama) --}}
                    <td class="px-8 py-5 text-sm text-gray-500">
                        {{ $obat->kategori->nama ?? 'Tanpa Kategori' }}
                    </td>

                    {{-- Tanggal Kadaluarsa --}}
                    <td class="px-8 py-5 text-center">
                        <span class="text-sm font-bold text-gray-700">{{ $tglKadaluarsa->format('d M Y') }}</span>
                    </td>

                    {{-- Status Sisa Hari --}}
                    <td class="px-8 py-5 text-center">
                        @if($diff < 0)
                            <span class="px-4 py-1.5 bg-red-600 text-white rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm">
                                <i class="fas fa-times-circle mr-1"></i> EXPIRED: {{ abs($diff) }} HARI LALU
                            </span>
                        @elseif($diff <= 7)
                            <span class="px-4 py-1.5 bg-red-100 text-red-600 rounded-full text-[10px] font-black uppercase tracking-wider border border-red-200">
                                <i class="fas fa-exclamation-circle mr-1"></i> KRITIS: {{ $diff }} HARI LAGI
                            </span>
                        @else
                            <span class="px-4 py-1.5 bg-orange-100 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-wider border border-orange-200">
                                <i class="fas fa-clock mr-1"></i> PERINGATAN: {{ $diff }} HARI
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-check-circle text-3xl text-green-400"></i>
                            </div>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Aman! Tidak ada obat kadaluarsa ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection