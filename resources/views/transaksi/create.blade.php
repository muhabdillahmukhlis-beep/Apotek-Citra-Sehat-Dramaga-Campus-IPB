@extends('layouts.app')

@section('header_title', 'Transaksi Baru')

@section('content')
<div class="max-w-7xl mx-auto pb-10" 
     id="kasir-container"
     data-obat='@json($obatList)'
     data-store-url="{{ route('transaksi.store') }}"
     data-index-url="{{ route('transaksi.index') }}"
     x-data="kasirApotek()">

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Sisi Kiri: Input & Keranjang --}}
        <div class="lg:w-2/3 space-y-6">
            <div class="bg-white rounded-[32px] shadow-sm border border-[#D4E8D4] p-8">
                <h3 class="font-bold text-[#1A2E1A] text-lg flex items-center gap-3 mb-6">
                    <i class="fas fa-search text-[#2E7D32]"></i> CARI OBAT
                </h3>

                <div class="relative mb-8">
                    <input type="text" 
                           x-model="search" 
                           @input.debounce.300ms="filterObat()"
                           placeholder="Ketik nama obat..." 
                           class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-500/20">
                    <i class="fas fa-pills absolute left-5 top-5 text-gray-300"></i>
                    
                    <div x-show="showResults" x-cloak @click.away="showResults = false" 
                         class="absolute z-10 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl max-h-60 overflow-y-auto">
                        <template x-for="obat in filteredObat" :key="obat.id">
                            <div @click="selectObat(obat)" class="p-4 hover:bg-green-50 cursor-pointer border-b border-gray-50 last:border-0 flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-gray-800" x-text="obat.nama"></p>
                                    <p class="text-[10px] text-gray-400 font-mono" x-text="obat.kode_obat || 'NO-CODE'"></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-[#2E7D32]" x-text="formatRupiah(obat.harga_jual)"></p>
                                    <p class="text-[10px] font-bold" :class="obat.stok > 10 ? 'text-orange-500' : 'text-red-500'" x-text="'Stok: ' + obat.stok"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Card Obat Terpilih --}}
                <div x-show="selectedObat" x-transition class="bg-[#F9FBF9] border border-[#D4E8D4] rounded-2xl p-6 flex flex-wrap items-center gap-6">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase">Obat Terpilih</p>
                        <p class="font-bold text-gray-800 text-lg" x-text="selectedObat?.nama"></p>
                    </div>
                    <div class="flex items-center gap-3 bg-white border border-gray-100 p-2 rounded-xl">
                        <button @click="qty > 1 ? qty-- : null" class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-lg font-bold">-</button>
                        <input type="number" x-model.number="qty" class="w-12 text-center font-bold focus:outline-none bg-transparent">
                        <button @click="qty < selectedObat?.stok ? qty++ : null" class="w-8 h-8 flex items-center justify-center bg-gray-50 rounded-lg font-bold">+</button>
                    </div>
                    <button @click="addToCart()" class="bg-[#2E7D32] hover:bg-green-700 text-white px-8 py-3 rounded-xl font-bold text-sm uppercase transition-all">
                        + Tambahkan
                    </button>
                </div>
            </div>

            {{-- Tabel Keranjang --}}
            <div class="bg-white rounded-[32px] shadow-sm border border-[#D4E8D4] p-8">
                <table class="w-full">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-widest border-b">
                            <th class="pb-4 text-left">Nama Obat</th>
                            <th class="pb-4 text-right">Harga</th>
                            <th class="pb-4 text-center">Jumlah</th>
                            <th class="pb-4 text-right">Subtotal</th>
                            <th class="pb-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <tr>
                                <td class="py-4 font-bold text-gray-800" x-text="item.nama"></td>
                                <td class="py-4 text-right text-gray-500" x-text="formatRupiah(item.harga)"></td>
                                <td class="py-4 text-center" x-text="item.qty"></td>
                                <td class="py-4 text-right font-bold text-gray-800" x-text="formatRupiah(item.harga * item.qty)"></td>
                                <td class="py-4 text-center">
                                    <button @click="removeFromCart(index)" class="text-red-300 hover:text-red-500">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="cart.length === 0">
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-sm italic">Keranjang masih kosong</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sisi Kanan: Pembayaran --}}
        <div class="lg:w-1/3">
            <div class="bg-[#1A2E1A] text-white rounded-[32px] p-8 shadow-2xl sticky top-24">
                <div class="text-center mb-10">
                    <p class="text-green-400/60 text-[10px] font-bold uppercase mb-2">Total Pembayaran</p>
                    <h2 class="text-5xl font-extrabold" x-text="formatRupiah(totalHarga)"></h2>
                </div>

                <div class="space-y-6 mb-8">
                    <div>
                        <p class="text-[10px] font-bold text-green-400/60 uppercase mb-3">Metode Pembayaran</p>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="method in ['Tunai', 'QRIS', 'Debit']">
                                <button @click="paymentMethod = method" 
                                        type="button"
                                        :class="paymentMethod === method ? 'bg-[#2E7D32] border-[#2E7D32]' : 'bg-white/5 border-white/10 text-gray-400'"
                                        class="py-3 rounded-xl text-[10px] font-black border transition-all" x-text="method"></button>
                            </template>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'Tunai'">
                        <label class="text-[10px] font-bold text-green-400 uppercase block mb-2">Uang Diterima</label>
                        <input type="number" x-model.number="uangDiterima" class="w-full bg-white/5 border border-white/10 p-4 rounded-2xl text-2xl font-bold focus:outline-none text-white">
                        <div class="flex justify-between mt-4">
                            <span class="text-gray-400 text-xs">Kembalian</span>
                            <span class="font-bold text-xl text-green-400" x-text="formatRupiah(kembalian)"></span>
                        </div>
                    </div>
                </div>

                <button @click="simpanTransaksi()" 
                        type="button"
                        :disabled="cart.length === 0 || (paymentMethod === 'Tunai' && uangDiterima < totalHarga)"
                        class="w-full bg-[#2E7D32] hover:bg-[#388E3C] disabled:opacity-50 disabled:grayscale text-white font-black py-5 rounded-2xl shadow-xl uppercase tracking-widest transition-all">
                    <span x-show="!loading">SIMPAN TRANSAKSI</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style> [x-cloak] { display: none !important; } </style>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    function kasirApotek() {
        return {
            search: '',
            qty: 1,
            selectedObat: null,
            showResults: false,
            paymentMethod: 'Tunai',
            uangDiterima: 0,
            cart: [],
            loading: false,
            // Perbaikan pengambilan data dari DOM
            obatList: JSON.parse(document.getElementById('kasir-container').dataset.obat),
            storeUrl: document.getElementById('kasir-container').dataset.storeUrl,
            indexUrl: document.getElementById('kasir-container').dataset.indexUrl,

            filterObat() {
                this.showResults = this.search.length > 1;
            },

            get filteredObat() {
                if (!this.search) return [];
                return this.obatList.filter(o => 
                    o.nama.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            selectObat(obat) {
                this.selectedObat = obat;
                this.search = obat.nama;
                this.showResults = false;
                this.qty = 1;
            },

            addToCart() {
                if (!this.selectedObat) return;
                
                // Cek apakah stok cukup
                if (this.qty > this.selectedObat.stok) {
                    alert('Stok tidak mencukupi!');
                    return;
                }

                const index = this.cart.findIndex(i => i.id === this.selectedObat.id);
                
                if (index > -1) {
                    this.cart[index].qty += this.qty;
                } else {
                    this.cart.push({
                        id: this.selectedObat.id,
                        nama: this.selectedObat.nama,
                        harga: this.selectedObat.harga_jual,
                        qty: this.qty,
                        stok: this.selectedObat.stok
                    });
                }
                this.selectedObat = null;
                this.search = '';
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
            },

            get totalHarga() {
                return this.cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            },

            get kembalian() {
                return Math.max(0, this.uangDiterima - this.totalHarga);
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { 
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0 
                }).format(number);
            },

            async simpanTransaksi() {
                if (this.cart.length === 0 || this.loading) return;
                
                const bayarFinal = this.paymentMethod === 'Tunai' ? this.uangDiterima : this.totalHarga;
                this.loading = true;

                try {
                    const response = await fetch(this.storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            items: this.cart,
                            metode_pembayaran: this.paymentMethod,
                            bayar: bayarFinal,
                            total: this.totalHarga
                        })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert('✅ Transaksi Berhasil!');
                        window.location.href = this.indexUrl;
                    } else {
                        // Tampilkan pesan error spesifik dari Laravel jika ada
                        alert('❌ Gagal: ' + (result.message || 'Terjadi kesalahan sistem'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan koneksi ke server. Pastikan server Laravel menyala.');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection