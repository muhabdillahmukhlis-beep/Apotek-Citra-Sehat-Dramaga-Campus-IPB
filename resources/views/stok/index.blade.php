@extends('layouts.app')

@section('header_title', 'Manajemen Stok')

@section('content')
<div class="container mx-auto p-4 lg:p-6 bg-gray-50 min-h-screen">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Manajemen Stok</h1>
        <p class="text-sm text-gray-500 font-medium">Monitor dan kelola inventaris obat secara real-time</p>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-xl shadow-sm flex items-center gap-3 animate-fade-in">
            <i class="fas fa-check-circle text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded-xl shadow-sm flex items-center gap-3 animate-fade-in">
            <i class="fas fa-exclamation-triangle text-lg"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="p-4 bg-green-50 rounded-2xl">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Total Jenis</p>
                <h3 class="text-2xl font-black text-gray-800 leading-none mt-1">{{ $totalJenis }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="p-4 bg-blue-50 rounded-2xl">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Total Unit</p>
                <h3 class="text-2xl font-black text-gray-800 leading-none mt-1">{{ number_format($totalUnit, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="p-4 bg-orange-50 rounded-2xl">
                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Perlu Restock</p>
                <h3 class="text-2xl font-black text-orange-600 leading-none mt-1">{{ $perluRestock }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Visualisasi Stok --}}
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <h4 class="font-bold text-gray-800 tracking-tight">Kondisi Stok Terkini</h4>
                <span class="text-[10px] bg-gray-100 px-2 py-1 rounded-md text-gray-500 font-bold uppercase">Top 10 Obat</span>
            </div>
            <div class="space-y-6">
                @foreach($obatVisual as $obat)
                <div class="group">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs font-bold text-gray-700 group-hover:text-green-700 transition-colors">{{ $obat->nama }}</span>
                        <span class="text-xs font-black {{ $obat->stok <= $obat->stok_minimum ? 'text-red-500' : 'text-gray-400' }}">
                            {{ $obat->stok }} / {{ $obat->stok_minimum }} min
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        @php
                            $persen = min(($obat->stok / max($obat->stok_minimum * 2.5, 1)) * 100, 100);
                            $warna = $obat->stok <= $obat->stok_minimum ? 'bg-red-500' : ($obat->stok <= ($obat->stok_minimum + 10) ? 'bg-orange-500' : 'bg-green-500');
                        @endphp
                        <div class="{{ $warna }} h-full transition-all duration-1000 ease-out shadow-sm" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Form Penyesuaian Stok --}}
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative">
            <h4 class="font-bold text-gray-800 mb-8 tracking-tight">Form Penyesuaian</h4>
            
            <form action="{{ route('stok.update') }}" method="POST" class="space-y-5" id="stokForm">
                @csrf
                {{-- @method('PATCH') --}} {{-- Aktifkan jika Route Anda memakai Patch --}}

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cari Data Obat</label>
                    <select name="obat_id" id="obat_select" required>
                        <option value="">-- Pilih Obat --</option>
                        @foreach($obatList as $o)
                            <option value="{{ $o->id }}">
                                {{ $o->nama }} (Tersedia: {{ $o->stok }} {{ $o->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Operasi</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="tambah" class="hidden peer" checked>
                            <div class="text-center p-3 rounded-2xl border-2 border-gray-100 text-xs font-black text-gray-400 peer-checked:bg-green-50 peer-checked:border-green-500 peer-checked:text-green-600 transition-all">
                                <i class="fas fa-plus-circle mb-1 block text-lg"></i> TAMBAH
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="kurang" class="hidden peer">
                            <div class="text-center p-3 rounded-2xl border-2 border-gray-100 text-xs font-black text-gray-400 peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-600 transition-all">
                                <i class="fas fa-minus-circle mb-1 block text-lg"></i> KURANGI
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jumlah Unit</label>
                        <input type="number" name="jumlah" required min="1" placeholder="0" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-green-500/20 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor Batch</label>
                        <input type="text" name="batch" placeholder="BCH-{{ date('Y') }}-XXX" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-green-500/20 transition-all uppercase">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alasan Penyesuaian</label>
                    <input type="text" name="alasan" required placeholder="Contoh: Barang Masuk Supplier / Obat Rusak" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-green-500/20 transition-all">
                </div>

                <button type="submit" id="submitBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-green-100 flex items-center justify-center gap-3">
                    <i class="fas fa-save text-lg"></i>
                    <span>SIMPAN PENYESUAIAN STOK</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    // Inisialisasi TomSelect
    const ts = new TomSelect("#obat_select",{
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Cari nama obat...",
    });

    // Dinamis Button Color & Text
    const radios = document.querySelectorAll('input[name="type"]');
    const submitBtn = document.getElementById('submitBtn');
    
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.value === 'kurang') {
                submitBtn.classList.replace('bg-green-600', 'bg-red-600');
                submitBtn.classList.replace('hover:bg-green-700', 'hover:bg-red-700');
                submitBtn.classList.replace('shadow-green-100', 'shadow-red-100');
                submitBtn.querySelector('span').innerText = 'KONFIRMASI PENGURANGAN STOK';
            } else {
                submitBtn.classList.replace('bg-red-600', 'bg-green-600');
                submitBtn.classList.replace('hover:bg-red-700', 'hover:bg-green-700');
                submitBtn.classList.replace('shadow-red-100', 'shadow-green-100');
                submitBtn.querySelector('span').innerText = 'SIMPAN PENYESUAIAN STOK';
            }
        });
    });
</script>

<style>
    /* Fix Z-Index TomSelect agar tidak tertutup */
    .ts-wrapper.single .ts-control {
        background-color: #f9fafb !important;
        border: none !important;
        padding: 1rem !important;
        border-radius: 1rem !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
    }
    .ts-dropdown {
        z-index: 100 !important;
        border-radius: 1rem !important;
        border: 1px solid #f3f4f6 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        padding: 0.5rem !important;
    }
    .ts-dropdown .active {
        background-color: #f0fdf4 !important;
        color: #166534 !important;
        border-radius: 0.5rem !important;
    }
</style>
@endpush