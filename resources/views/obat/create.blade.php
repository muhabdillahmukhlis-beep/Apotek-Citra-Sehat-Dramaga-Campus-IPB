@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F0F4F0] pb-24">
    <div class="bg-white px-6 py-5 border-b border-[#D4E8D4] flex items-center gap-4 sticky top-0 z-40">
        <a href="{{ route('obat.index') }}" class="text-[#7A8C7A] hover:text-[#2E7D32] transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="font-sora font-extrabold text-[#1A2E1A] text-lg leading-none">Tambah Obat</h1>
            <p class="text-[10px] font-bold text-[#7A8C7A] tracking-widest uppercase mt-1">Input data inventori baru</p>
        </div>
    </div>

    <div class="p-6">
        <form action="{{ route('obat.store') }}" method="POST" class="space-y-6 max-w-4xl mx-auto">
            @csrf
            <div class="bg-white p-8 rounded-[28px] border border-[#D4E8D4] space-y-6 shadow-sm">
                
                <div>
                    <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Nama Produk</label>
                    <input type="text" name="nama" required 
                           class="w-full h-14 px-5 bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl focus:border-[#2E7D32] outline-none text-sm font-bold text-[#1A2E1A] transition-all" 
                           placeholder="Masukkan nama lengkap obat...">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Kategori</label>
                        <select name="id_kategori" required 
                                class="w-full h-14 px-5 bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl focus:border-[#2E7D32] outline-none text-sm font-bold text-[#1A2E1A] appearance-none bg-white transition-all">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Satuan</label>
                        <select name="satuan" required 
                                class="w-full h-14 px-5 bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl focus:border-[#2E7D32] outline-none text-sm font-bold text-[#1A2E1A] appearance-none bg-white transition-all">
                            <option value="Strip">Strip</option>
                            <option value="Botol">Botol</option>
                            <option value="Box">Box</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Tablet">Tablet</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Stok</label>
                        <input type="number" name="stok" required 
                               class="w-full h-14 px-5 bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl focus:border-[#2E7D32] outline-none text-sm font-bold text-[#2E7D32] transition-all" 
                               placeholder="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Kadaluarsa</label>
                        <input type="date" name="tgl_kadaluarsa" required 
                               class="w-full h-14 px-5 bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl focus:border-[#2E7D32] outline-none text-sm font-bold text-[#1A2E1A] transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Harga Beli</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-bold text-[#7A8C7A]">Rp</span>
                            <input type="number" name="harga_beli" required 
                                   class="w-full h-14 pl-12 pr-5 bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl focus:border-[#2E7D32] outline-none text-sm font-bold text-[#1A2E1A] transition-all" 
                                   placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Harga Jual</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-bold text-[#2E7D32]">Rp</span>
                            <input type="number" name="harga_jual" required 
                                   class="w-full h-14 pl-12 pr-5 bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl focus:border-[#2E7D32] outline-none text-sm font-black text-[#2E7D32] transition-all" 
                                   placeholder="0">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-[#F0F4F0]">
                    <label class="block text-[10px] font-bold text-[#7A8C7A] uppercase mb-2 ml-1">Kode Obat (Internal)</label>
                    <input type="text" name="kode_obat" required 
                           class="w-full h-12 px-5 bg-white border border-[#D4E8D4] rounded-xl focus:border-[#2E7D32] outline-none text-xs font-mono font-bold text-[#7A8C7A] transition-all" 
                           placeholder="Contoh: OBT-001">
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('obat.index') }}" class="flex-1 h-14 bg-white border border-[#D4E8D4] text-[#7A8C7A] font-bold rounded-2xl flex items-center justify-center hover:bg-gray-50 transition-all">
                    BATAL
                </a>
                <button type="submit" class="flex-[2] h-14 bg-[#2E7D32] text-white font-bold rounded-2xl shadow-lg shadow-green-900/20 active:scale-95 hover:bg-[#1B5E20] transition-all">
                    SIMPAN DATA OBAT
                </button>
            </div>
        </form>
    </div>
</div>
@endsection