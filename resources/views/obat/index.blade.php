@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F0F4F0] pb-24">
    <div class="bg-white px-6 py-5 border-b border-[#D4E8D4] sticky top-0 z-40 flex justify-between items-center">
        <div>
            <h1 class="font-sora font-extrabold text-[#1A2E1A] text-lg leading-none">Data Obat</h1>
            <p class="text-[10px] font-bold text-[#7A8C7A] tracking-widest uppercase mt-1">Kelola inventori dan data master obat apotek</p>
        </div>
        
        <div class="flex items-center gap-2">
            <form action="{{ route('obat.import') }}" method="POST" enctype="multipart/form-data" id="importForm" class="hidden">
                @csrf
                <input type="file" name="file" id="fileInput" accept=".xlsx, .xls" onchange="document.getElementById('importForm').submit()">
            </form>
            
            <button onclick="document.getElementById('fileInput').click()" class="px-4 h-10 bg-white border border-[#D4E8D4] rounded-xl flex items-center gap-2 text-[11px] font-bold text-[#2E7D32] hover:bg-gray-50 active:scale-95 transition-all">
                <i class="fas fa-upload"></i> Import Excel
            </button>

            <a href="{{ route('obat.export') }}" class="px-4 h-10 bg-white border border-[#D4E8D4] rounded-xl flex items-center gap-2 text-[11px] font-bold text-blue-600 hover:bg-gray-50 active:scale-95 transition-all">
                <i class="fas fa-download"></i> Export
            </a>

            <a href="{{ route('obat.create') }}" class="px-4 h-10 bg-[#2E7D32] rounded-xl flex items-center gap-2 text-[11px] font-bold text-white shadow-lg shadow-green-900/20 active:scale-95 transition-all">
                <i class="fas fa-plus"></i> Tambah Obat
            </a>
        </div>
    </div>

    <div class="p-6 space-y-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl text-xs font-bold mb-4">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl text-xs font-bold mb-4">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[300px]">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-[#7A8C7A] text-xs"></i>
                <input type="text" 
                       name="search" 
                       id="searchInput" 
                       placeholder="Cari nama obat, ID, barcode..." 
                       value="{{ request('search') }}" 
                       class="w-full h-11 pl-10 pr-4 bg-white border border-[#D4E8D4] rounded-2xl text-xs font-medium focus:border-[#2E7D32] outline-none shadow-sm">
            </div>

            <select id="filterKategori" onchange="applyFilters()" class="h-11 px-4 bg-white border border-[#D4E8D4] rounded-2xl text-xs font-bold text-[#7A8C7A] outline-none focus:border-[#2E7D32] shadow-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('kategori') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama }}
                    </option>
                @endforeach
            </select>

            <select id="filterStatus" onchange="applyFilters()" class="h-11 px-4 bg-white border border-[#D4E8D4] rounded-2xl text-xs font-bold text-[#7A8C7A] outline-none focus:border-[#2E7D32] shadow-sm">
                <option value="">Semua Status</option>
                <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Aman</option>
                <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>Menipis</option>
                <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($obat as $item)
            <div class="bg-white rounded-[28px] border border-[#D4E8D4] overflow-hidden shadow-sm shadow-green-900/5 transition-all hover:shadow-md">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-[10px] font-mono font-bold text-[#2E7D32] bg-[#E8F5E9] px-2 py-1 rounded-lg">#{{ $item->kode_obat }}</span>
                        
                        @php
                            if($item->stok <= 0) {
                                $status_label = 'HABIS';
                                $status_color = 'bg-red-100 text-red-600 border-red-200';
                            } elseif($item->stok <= 10) {
                                $status_label = 'MENIPIS';
                                $status_color = 'bg-orange-100 text-orange-600 border-orange-200';
                            } else {
                                $status_label = 'AMAN';
                                $status_color = 'bg-green-100 text-green-600 border-green-200';
                            }

                            // Pastikan tanggal diparsing dengan Carbon agar tidak error
                            $tgl_kadaluarsa = $item->tgl_kadaluarsa ? \Carbon\Carbon::parse($item->tgl_kadaluarsa) : null;
                        @endphp
                        <span class="text-[9px] font-black border {{ $status_color }} px-2 py-1 rounded-full tracking-tighter">{{ $status_label }}</span>
                    </div>

                    <div class="mb-4">
                        <h3 class="font-sora font-extrabold text-[#1A2E1A] text-base leading-tight mb-1 uppercase">{{ $item->nama }}</h3>
                        <p class="text-[11px] font-bold text-[#7A8C7A]">
                            <i class="fas fa-tag mr-1"></i> {{ $item->kategori->nama ?? 'Umum' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-y-4 border-t border-[#F0F4F0] pt-4 mb-4">
                        <div>
                            <p class="text-[9px] font-bold text-[#7A8C7A] uppercase mb-1">Harga Beli</p>
                            <p class="text-xs font-bold text-gray-600">Rp{{ number_format($item->harga_beli ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-[#7A8C7A] uppercase mb-1">Harga Jual</p>
                            <p class="text-sm font-black text-[#2E7D32]">Rp{{ number_format($item->harga_jual ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-[#7A8C7A] uppercase mb-1">Stok Saat Ini</p>
                            <p class="text-sm font-black text-[#1A2E1A]">{{ $item->stok }} <span class="text-[10px] text-[#7A8C7A] font-medium">{{ $item->satuan }}</span></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-[#7A8C7A] uppercase mb-1">Kadaluarsa</p>
                            <p class="text-xs font-bold {{ $tgl_kadaluarsa && $tgl_kadaluarsa->isPast() ? 'text-red-500' : 'text-[#1A2E1A]' }}">
                                {{ $tgl_kadaluarsa ? $tgl_kadaluarsa->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('obat.edit', $item->id) }}" class="flex-1 h-11 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center gap-2 text-xs font-bold active:scale-95 transition-all">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('obat.destroy', $item->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus obat ini?')">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="w-full h-11 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center gap-2 text-xs font-bold active:scale-95 transition-all">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-20 bg-white rounded-[28px] border border-dashed border-[#D4E8D4]">
                <i class="fas fa-box-open text-4xl text-gray-200 mb-4"></i>
                <p class="text-[#7A8C7A] font-bold">Tidak ada data obat yang ditemukan.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const filterKategori = document.getElementById('filterKategori');
    const filterStatus = document.getElementById('filterStatus');

    function applyFilters() {
        const url = new URL(window.location.href);
        const search = searchInput.value;
        const kategori = filterKategori.value;
        const status = filterStatus.value;

        if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
        if (kategori) url.searchParams.set('kategori', kategori); else url.searchParams.delete('kategori');
        if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');

        window.location.href = url.toString();
    }

    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
</script>
@endsection