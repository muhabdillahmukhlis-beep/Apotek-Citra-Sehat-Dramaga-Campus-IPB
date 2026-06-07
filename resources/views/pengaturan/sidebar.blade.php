@php
    // Menyeragamkan string role agar aman dari perbedaan kapitalisasi
    $roleUser = strtolower(trim(auth()->user()->role ?? ''));
@endphp

<div class="bg-white p-4 rounded-[24px] border border-[#D4E8D4] shadow-sm space-y-1">
    
    {{-- 1. Menu Profil Saya (Bisa diakses Semua Role) --}}
    <a href="{{ route('pengaturan.index') }}" 
       class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pengaturan.index') ? 'bg-[#E8F5E9] text-[#2E7D32] font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-semibold' }} rounded-xl text-xs transition-all">
        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pengaturan.index') ? 'bg-[#2E7D32]' : 'bg-transparent' }}"></span>
        <i class="fas fa-user w-4 text-center"></i> Profil Saya
    </a>
    
    {{-- 2. Hak Akses Penuh: Admin & Pemilik dapat melihat & mengklik menu --}}
    @if(in_array($roleUser, ['admin', 'pemilik']))
        
        {{-- Info Apotek (SINKRONISASI: Menggunakan 'sistem.index') --}}
        <a href="{{ route('sistem.index') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('sistem.index') ? 'bg-[#E8F5E9] text-[#2E7D32] font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-semibold' }} rounded-xl text-xs transition-all">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('sistem.index') ? 'bg-[#2E7D32]' : 'bg-transparent' }}"></span>
            <i class="fas fa-clinic-medical w-4 text-center"></i> Info Apotek
        </a>
        
        {{-- Keamanan --}}
        <a href="{{ route('pengaturan.keamanan') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pengaturan.keamanan') ? 'bg-[#E8F5E9] text-[#2E7D32] font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-semibold' }} rounded-xl text-xs transition-all">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pengaturan.keamanan') ? 'bg-[#2E7D32]' : 'bg-transparent' }}"></span>
            <i class="fas fa-shield-alt w-4 text-center"></i> Keamanan
        </a>

        {{-- Format Struk --}}
        <a href="{{ route('pengaturan.struk') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pengaturan.struk') ? 'bg-[#E8F5E9] text-[#2E7D32] font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-semibold' }} rounded-xl text-xs transition-all">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pengaturan.struk') ? 'bg-[#2E7D32]' : 'bg-transparent' }}"></span>
            <i class="fas fa-print w-4 text-center"></i> Format Struk
        </a>

        {{-- Backup & Restore --}}
        <a href="{{ route('pengaturan.backup') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pengaturan.backup') ? 'bg-[#E8F5E9] text-[#2E7D32] font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-semibold' }} rounded-xl text-xs transition-all">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pengaturan.backup') ? 'bg-[#2E7D32]' : 'bg-transparent' }}"></span>
            <i class="fas fa-cloud-upload-alt w-4 text-center"></i> Backup & Restore
        </a>

        {{-- Log Audit --}}
        <a href="{{ route('pengaturan.log_audit') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pengaturan.log_audit') ? 'bg-[#E8F5E9] text-[#2E7D32] font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-semibold' }} rounded-xl text-xs transition-all">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pengaturan.log_audit') ? 'bg-[#2E7D32]' : 'bg-transparent' }}"></span>
            <i class="fas fa-history w-4 text-center"></i> Log Audit
        </a>

        {{-- Notifikasi --}}
        <a href="{{ route('pengaturan.notifikasi') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('pengaturan.notifikasi') ? 'bg-[#E8F5E9] text-[#2E7D32] font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-semibold' }} rounded-xl text-xs transition-all">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pengaturan.notifikasi') ? 'bg-[#2E7D32]' : 'bg-transparent' }}"></span>
            <i class="fas fa-bell w-4 text-center"></i> Notifikasi
        </a>

    @else
        {{-- 3. Jika Kasir / Apoteker login, kunci menu dengan visual abu-abu (Disabled) --}}
        <div class="pt-2 mt-2 border-t border-gray-100 space-y-1 opacity-60 select-none">
            <div class="flex items-center gap-3 px-4 py-2.5 text-gray-400 text-xs font-medium cursor-not-allowed">
                <i class="fas fa-clinic-medical w-4 text-center"></i> Info Apotek
            </div>
            <div class="flex items-center gap-3 px-4 py-2.5 text-gray-400 text-xs font-medium cursor-not-allowed">
                <i class="fas fa-shield-alt w-4 text-center"></i> Keamanan
            </div>
            <div class="flex items-center gap-3 px-4 py-2.5 text-gray-400 text-xs font-medium cursor-not-allowed">
                <i class="fas fa-print w-4 text-center"></i> Format Struk
            </div>
            <div class="flex items-center gap-3 px-4 py-2.5 text-gray-400 text-xs font-medium cursor-not-allowed">
                <i class="fas fa-cloud-upload-alt w-4 text-center"></i> Backup & Restore
            </div>
            <div class="flex items-center gap-3 px-4 py-2.5 text-gray-400 text-xs font-medium cursor-not-allowed">
                <i class="fas fa-history w-4 text-center"></i> Log Audit
            </div>
            <div class="flex items-center gap-3 px-4 py-2.5 text-gray-400 text-xs font-medium cursor-not-allowed">
                <i class="fas fa-bell w-4 text-center"></i> Notifikasi
            </div>
        </div>
    @endif
</div>