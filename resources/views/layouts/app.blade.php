<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('header_title', 'Dashboard') — Apotek Citra Sehat</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-sora { font-family: 'Sora', sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { 
            background: rgba(255,255,255,0.1); 
            border-radius: 10px; 
        }
    </style>
</head>
<body class="bg-[#F0F4F0]">
    <div class="flex min-h-screen">
        
        <aside class="w-72 bg-[#1B5E20] text-white flex flex-col fixed h-full z-20 shadow-xl">
            <div class="p-8 flex-1 overflow-hidden flex flex-col">
                <div class="flex items-center gap-3 mb-10">
                    <div class="bg-white p-2 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 fill-[#1B5E20]" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-sm font-extrabold tracking-tight font-sora leading-none uppercase">Apotek Citra Sehat</h1>
                        <p class="text-[9px] text-green-300 mt-1 font-bold tracking-widest uppercase">IPB Dramaga Campus</p>
                    </div>
                </div>
                
                <nav class="space-y-6 sidebar-scroll overflow-y-auto pr-2">
                    <div>
                        <p class="text-[10px] font-bold text-green-400/50 tracking-widest uppercase mb-3 ml-2">Utama</p>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#2E7D32] font-bold shadow-lg shadow-black/10' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }}">
                                <i class="fa-solid fa-chart-pie text-lg"></i> <span class="text-sm">Dashboard</span>
                            </a>
                            <a href="{{ route('obat.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('obat.*') ? 'bg-[#2E7D32] font-bold shadow-lg shadow-black/10' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }}">
                                <i class="fa-solid fa-pills text-lg"></i> <span class="text-sm">Data Obat</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-green-400/50 tracking-widest uppercase mb-3 ml-2">Penjualan</p>
                        <div class="space-y-1">
                            <a href="{{ route('transaksi.create') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('transaksi.create') ? 'bg-[#2E7D32] font-bold shadow-lg' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }}">
                                <div class="flex items-center gap-4">
                                    <i class="fa-solid fa-cart-shopping text-lg"></i> <span class="text-sm">Transaksi Baru</span>
                                </div>
                                <span class="bg-green-500 text-[9px] px-1.5 py-0.5 rounded font-bold text-white shadow-sm">F2</span>
                            </a>
                            <a href="{{ route('transaksi.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('transaksi.index') ? 'bg-[#2E7D32] font-bold shadow-lg' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }}">
                                <i class="fa-solid fa-clock-rotate-left text-lg"></i> <span class="text-sm">Riwayat Transaksi</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-green-400/50 tracking-widest uppercase mb-3 ml-2">Inventori & Analitik</p>
                        <div class="space-y-1">
                            <a href="{{ route('stok.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('stok.*') ? 'bg-[#2E7D32] font-bold shadow-lg' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }}">
                                <i class="fa-solid fa-boxes-stacked text-lg"></i> <span class="text-sm">Manajemen Stok</span>
                            </a>
                            
                            <a href="{{ route('obat.expired') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('obat.expired') ? 'bg-[#2E7D32] font-bold shadow-lg' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }}">
                                <div class="flex items-center gap-4">
                                    <i class="fa-solid fa-hourglass-half text-lg"></i> <span class="text-sm">Monitoring Expired</span>
                                </div>
                                <span class="bg-red-500 text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-bold shadow-lg text-white">5</span>
                            </a>

                            <a href="{{ route('laporan.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('laporan.*') ? 'bg-[#2E7D32] font-bold shadow-lg' : 'text-gray-300 hover:bg-[#2E7D32]/50 hover:text-white' }}">
                                <i class="fa-solid fa-chart-line text-lg"></i> <span class="text-sm">Laporan Analitik</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="p-6 bg-[#144316]">
                <div class="flex items-center gap-3 mb-6 px-2">
                    <div class="w-10 h-10 bg-[#2E7D32] rounded-xl flex items-center justify-center font-bold text-white shadow-inner uppercase border border-white/10">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[9px] text-green-400 font-bold uppercase tracking-wider">
                            {{ auth()->user()->role ?? 'Apoteker' }}
                        </p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center justify-center gap-3 p-3 w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-xl text-xs font-bold transition-all border border-red-500/20">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> KELUAR SISTEM
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 ml-72 min-h-screen flex flex-col overflow-x-hidden">
            
            <header class="h-20 bg-white border-b border-[#D4E8D4] flex items-center justify-between px-10 sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-8 bg-[#2E7D32] rounded-full shadow-sm"></div>
                    <h2 class="font-bold text-gray-800 tracking-tight text-lg uppercase font-sora">
                        @yield('header_title', 'Sistem Informasi Apotek')
                    </h2>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-sora">Hari Ini</span>
                        <span class="text-sm font-bold text-[#1A2E1A]">{{ date('d F Y') }}</span>
                    </div>
                    <button class="relative w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:text-[#2E7D32] transition-all border border-gray-100 group">
                        <i class="fa-solid fa-bell"></i>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white text-[8px] flex items-center justify-center text-white font-black group-hover:scale-110 transition-transform">3</span>
                    </button>
                </div>
            </header>

            <div class="p-8 flex-1">
                @yield('content')
            </div>

            <footer class="p-8 text-center text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} Apotek Citra Sehat — Dashboard v1.0
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>