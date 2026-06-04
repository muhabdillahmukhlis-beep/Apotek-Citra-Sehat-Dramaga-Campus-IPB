@extends('layouts.app')

@section('header_title', 'Pusat Notifikasi')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- Bagian Header Utama Atas -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <!-- Filter Tabs (Sekarang Menggunakan Kelas Tab-Filter dan Atribut Data) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
            <button data-target="all" class="tab-filter px-5 py-2 rounded-full text-xs font-bold bg-[#2E7D32] text-white shadow-md shadow-green-900/10 transition-all whitespace-nowrap">
                Semua (5)
            </button>
            <button data-target="stok" class="tab-filter px-5 py-2 rounded-full text-xs font-semibold bg-white text-gray-600 hover:bg-gray-50 border border-gray-200/80 transition-all whitespace-nowrap">
                Stok Kritis (2)
            </button>
            <button data-target="kadaluarsa" class="tab-filter px-5 py-2 rounded-full text-xs font-semibold bg-white text-gray-600 hover:bg-gray-50 border border-gray-200/80 transition-all whitespace-nowrap">
                Kadaluarsa (1)
            </button>
            <button data-target="sistem" class="tab-filter px-5 py-2 rounded-full text-xs font-semibold bg-white text-gray-600 hover:bg-gray-50 border border-gray-200/80 transition-all whitespace-nowrap">
                Sistem (2)
            </button>
        </div>

        <!-- Tombol Aksi Tambahan -->
        <button id="btnMarkAll" class="px-4 py-2 text-xs font-bold text-[#2E7D32] bg-white border-2 border-[#2E7D32]/10 hover:border-[#2E7D32] rounded-xl transition-all shadow-sm whitespace-nowrap">
            <i class="fa-solid fa-check-double mr-1.5"></i> Tandai Semua Dibaca
        </button>
    </div>

    <!-- Container Utama List Notifikasi -->
    <div class="bg-white rounded-3xl border border-[#D4E8D4]/60 shadow-sm overflow-hidden divide-y divide-gray-100">
        @forelse($notifikasis as $notif)
            <!-- Ditambahkan atribut data-category untuk dibaca oleh JavaScript -->
            <div data-category="{{ $notif['kategori'] }}" class="notif-item p-6 flex items-start gap-4 transition-all hover:bg-gray-50/50 relative {{ !$notif['dibaca'] ? 'bg-green-50/20' : '' }}">
                
                <!-- Icon Kategori Kiri -->
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 text-base {{ $notif['icon_bg'] }}">
                    <i class="{{ $notif['icon'] }}"></i>
                </div>

                <!-- Konten Teks Tengah -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <h3 class="text-sm font-bold text-gray-900 tracking-tight leading-snug">
                            {{ $notif['judul'] }}
                        </h3>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed mb-2 font-medium">
                        {{ $notif['pesan'] }}
                    </p>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">
                        <i class="fa-regular fa-clock mr-1"></i> {{ $notif['waktu'] }}
                    </span>
                </div>

                <!-- Indikator Bulatan Hijau Kanan (Belum Dibaca) -->
                @if(!$notif['dibaca'])
                    <div class="notif-dot flex items-center self-center pr-2">
                        <span class="w-2.5 h-2.5 bg-[#2E7D32] rounded-full shadow-sm shadow-green-800/30"></span>
                    </div>
                @endif
                
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="fa-solid fa-bell-slash text-xl"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-800 mb-1">Tidak ada notifikasi</h4>
                <p class="text-xs text-gray-400">Semua pemberitahuan sistem operasional apotek telah bersih.</p>
            </div>
        @endforelse

        <!-- Kontainer khusus jika hasil filter kosong -->
        <div id="emptyFilterMessage" class="p-12 text-center hidden">
            <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                <i class="fa-solid fa-bell-slash text-xl"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-800 mb-1">Tidak ada pemberitahuan</h4>
            <p class="text-xs text-gray-400">Tidak ada notifikasi untuk kategori ini.</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filters = document.querySelectorAll('.tab-filter');
        const items = document.querySelectorAll('.notif-item');
        const emptyMessage = document.getElementById('emptyFilterMessage');

        // --- 1. LOGIKA INTERAKSI FILTER KLIK ---
        filters.forEach(button => {
            button.addEventListener('click', function () {
                const target = this.getAttribute('data-target');

                // Atur ulang gaya tombol (kembalikan semua ke putih/bawaan)
                filters.forEach(btn => {
                    btn.className = "tab-filter px-5 py-2 rounded-full text-xs font-semibold bg-white text-gray-600 hover:bg-gray-50 border border-gray-200/80 transition-all whitespace-nowrap";
                });

                // Berikan gaya aktif (hijau) pada tombol yang sedang di-klik
                this.className = "tab-filter px-5 py-2 rounded-full text-xs font-bold bg-[#2E7D32] text-white shadow-md shadow-green-900/10 transition-all whitespace-nowrap";

                // Saring data item notifikasi
                let visibleCount = 0;
                items.forEach(item => {
                    if (target === 'all' || item.getAttribute('data-category') === target) {
                        item.style.setProperty('display', 'flex', 'important');
                        visibleCount++;
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });

                // Tampilkan pesan kosong jika tidak ada data yang lolos filter
                if (visibleCount === 0) {
                    emptyMessage.classList.remove('hidden');
                } else {
                    emptyMessage.classList.add('hidden');
                }
            });
        });

        // --- 2. LOGIKA TOMBOL TANDAI SEMUA DIBACA ---
        const btnMarkAll = document.getElementById('btnMarkAll');
        if (btnMarkAll) {
            btnMarkAll.addEventListener('click', function () {
                // Hapus latar belakang hijau muda pada baris yang belum dibaca
                items.forEach(item => {
                    item.classList.remove('bg-green-50/20');
                    
                    // Sembunyikan bulatan hijau penanda belum dibaca di sebelah kanan
                    const dot = item.querySelector('.notif-dot');
                    if (dot) {
                        dot.remove();
                    }
                });
                
                alert('Semua notifikasi berhasil ditandai sebagai dibaca!');
            });
        }
    });
</script>
@endpush