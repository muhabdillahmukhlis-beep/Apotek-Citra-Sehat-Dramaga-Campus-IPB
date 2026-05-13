@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F0F4F0] pb-24">
    <div class="bg-white px-6 py-5 border-b border-[#D4E8D4] sticky top-0 z-40 flex items-center gap-4">
        <a href="{{ route('obat.index') }}" class="w-10 h-10 flex items-center justify-center bg-[#F0F4F0] rounded-xl text-[#2E7D32] active:scale-90 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="font-sora font-extrabold text-[#1A2E1A] text-lg leading-none">Edit Data Obat</h1>
            
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-lg mt-3 shadow-sm">
                    <p class="font-bold text-xs uppercase mb-1">Ada kesalahan pengisian:</p>
                    <ul class="list-disc list-inside text-[11px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-[10px] font-bold text-[#7A8C7A] tracking-widest uppercase mt-2">Kode: {{ $obat->kode_obat }}</p>
        </div>
    </div>

    <div class="p-6">
        <form action="{{ route('obat.update', $obat->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" name="kode_obat" value="{{ $obat->kode_obat }}">

            <div class="bg-white p-6 rounded-[32px] border border-[#D4E8D4] shadow-sm space-y-5">
                
                <div>
                    <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Nama Produk</label>
                    <input type="text" name="nama" value="{{ old('nama', $obat->nama) }}" required
                        class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#1A2E1A] font-bold focus:border-[#2E7D32] focus:outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Kategori</label>
                        <div class="relative">
                            <select name="id_kategori" required
                                class="w-full h-14 px-4 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-gray-900 font-bold focus:border-[#2E7D32] outline-none appearance-none transition-all cursor-pointer">
                                <option value="">-- Pilih --</option>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->id }}" {{ (old('id_kategori', $obat->id_kategori) == $k->id) ? 'selected' : '' }}>
                                        {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-[#2E7D32]">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Satuan</label>
                        <input type="text" name="satuan" value="{{ old('satuan', $obat->satuan) }}" placeholder="Strip/Botol" required
                            class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#1A2E1A] font-bold focus:border-[#2E7D32] focus:outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Stok</label>
                        <input type="number" name="stok" value="{{ old('stok', $obat->stok) }}" required
                            class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#2E7D32] font-black text-lg focus:border-[#2E7D32] focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Kadaluarsa</label>
                        <input type="date" name="tgl_kadaluarsa" 
                            value="{{ old('tgl_kadaluarsa', $obat->tgl_kadaluarsa ? $obat->tgl_kadaluarsa->format('Y-m-d') : '') }}" required
                            class="w-full h-14 px-4 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#1A2E1A] font-bold focus:border-[#2E7D32] focus:outline-none transition-all text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Harga Beli</label>
                        <input type="number" name="harga_beli" value="{{ old('harga_beli', $obat->harga_beli) }}" required
                            class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#1A2E1A] font-bold focus:border-[#2E7D32] focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Harga Jual</label>
                        <input type="number" name="harga_jual" value="{{ old('harga_jual', $obat->harga_jual) }}" required
                            class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#2E7D32] font-black focus:border-[#2E7D32] focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button type="submit" class="w-full h-16 bg-[#2E7D32] text-white font-sora font-extrabold text-lg rounded-[24px] shadow-xl shadow-green-900/20 active:scale-95 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-save text-sm"></i> SIMPAN PERUBAHAN
                </button>
                
                <a href="{{ route('obat.index') }}" class="w-full h-16 bg-white text-[#7A8C7A] font-bold rounded-[24px] flex items-center justify-center border border-[#D4E8D4] active:scale-95 transition-all">
                    BATALKAN
                </a>
            </div>
        </form>
    </div>
</div>
@endsection