<aside class="w-64 min-h-screen bg-[#1B5E20] text-white flex flex-col py-6 fixed left-0 top-0 z-20 shadow-2xl">
    <div class="px-6 mb-8">
        <div class="flex items-center gap-3">
            <div class="bg-white p-1.5 rounded-lg shadow-lg">
                <i class="fas fa-plus text-[#1B5E20] text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-sm leading-tight uppercase tracking-tight">Apotek Citra Sehat</h2>
                <p class="text-[9px] text-green-300 font-bold tracking-widest uppercase">IPB Dramaga Campus</p>
            </div>
        </div>
    </div>

    <div class="flex-1 px-4 space-y-6 overflow-y-auto custom-scrollbar">
        <div>
            <p class="px-2 mb-2 text-[10px] font-bold text-green-400/50 tracking-widest uppercase">Utama</p>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-[#2E7D32] shadow-md border border-white/10' : 'hover:bg-[#2E7D32]/50 text-gray-300 hover:text-white' }} transition-all group">
                    <i class="fas fa-th-large w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
                <a href="{{ route('obat.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('obat.index') ? 'bg-[#2E7D32] shadow-md border border-white/10' : 'hover:bg-[#2E7D32]/50 text-gray-300 hover:text-white' }} transition-all group">
                    <i class="fas fa-pills w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium">Data Obat</span>
                </a>
            </div>
        </div>

        <div>
            <p class="px-2 mb-2 text-[10px] font-bold text-green-400/50 tracking-widest uppercase">Penjualan</p>
            <div class="space-y-1">
                <a href="{{ route('transaksi.create') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl {{ request()->routeIs('transaksi.create') ? 'bg-[#2E7D32] shadow-md border border-white/10' : 'hover:bg-[#2E7D32]/50 text-gray-300 hover:text-white' }} transition-all group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shopping-cart w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-medium">Transaksi Baru</span>
                    </div>
                    <span class="bg-green-500 text-[9px] px-1.5 py-0.5 rounded-md font-bold text-white shadow-sm">F2</span>
                </a>
                <a href="{{ route('transaksi.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('transaksi.index') ? 'bg-[#2E7D32] shadow-md border border-white/10' : 'hover:bg-[#2E7D32]/50 text-gray-300 hover:text-white' }} transition-all group">
                    <i class="fas fa-history w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium">Riwayat Transaksi</span>
                </a>
            </div>
        </div>

        <div>
            <p class="px-2 mb-2 text-[10px] font-bold text-green-400/50 tracking-widest uppercase">Inventori</p>
            <div class="space-y-1">
                <a href="{{ route('stok.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('stok.*') ? 'bg-[#2E7D32] shadow-md border border-white/10' : 'hover:bg-[#2E7D32]/50 text-gray-300 hover:text-white' }} transition-all group">
                    <i class="fas fa-boxes w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium">Manajemen Stok</span>
                </a>
                <a href="{{ route('obat.expired') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl {{ request()->routeIs('obat.expired') ? 'bg-[#2E7D32] shadow-md border border-white/10' : 'hover:bg-[#2E7D32]/50 text-gray-300 hover:text-white' }} transition-all group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-medium">Monitoring Expired</span>
                    </div>
                    <span class="bg-red-500 text-[10px] px-2 py-0.5 rounded-full font-bold text-white animate-pulse">5</span>
                </a>
                
                <a href="{{ route('laporan.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('laporan.*') ? 'bg-[#2E7D32] text-white shadow-lg border border-white/10' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }} transition-all group mt-1">
                    <i class="fas fa-chart-line w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium">Laporan Analitik</span>
                </a>
            </div>
        </div>

        <div>
            <p class="px-2 mb-2 text-[10px] font-bold text-green-400/50 tracking-widest uppercase">Sistem</p>
            <div class="space-y-1 text-gray-300">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#2E7D32]/50 transition-all cursor-not-allowed opacity-50">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="text-sm font-medium">Pengaturan</span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 mt-auto pt-6 border-t border-green-800/50">
        <div class="flex items-center justify-between p-3 bg-[#144316] rounded-2xl border border-white/5">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#2E7D32] to-[#1B5E20] flex items-center justify-center font-bold text-xs uppercase shadow-inner border border-white/10 shrink-0">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-[11px] font-bold leading-none truncate text-white">{{ Auth::user()->name ?? 'Administrator' }}</h4>
                    <p class="text-[9px] text-green-400 mt-1 uppercase font-bold tracking-tighter">{{ Auth::user()->role ?? 'Apoteker' }}</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="shrink-0 ml-2">
                @csrf
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar?')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-red-500 hover:text-white transition-all">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
</style>