@extends('layouts.app')

@section('header_title', 'Pengaturan Profil')

@section('content')
<div class="space-y-6 w-full max-w-7xl mx-auto pb-12">

    {{-- Header Judul Halaman --}}
    <div>
        <h2 class="text-2xl font-black text-[#1A2E1A]">Pengaturan & Profil</h2>
    </div>

    {{-- Notifikasi Alert Sukses --}}
    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-100 font-semibold flex items-center gap-2 animate-fade-in">
        <i class="fas fa-check-circle text-green-600"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Notifikasi Alert Error Validasi --}}
    @if($errors->any())
    <div class="p-4 mb-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-100 font-semibold animate-fade-in">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <span>Terjadi kesalahan validasi:</span>
        </div>
        <ul class="list-disc list-inside pl-2 text-xs font-normal space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Layout Grid Utama: Menu Samping & Form Konten --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
        
        {{-- 🌟 PEMANGGILAN KOMPONEN SIDEBAR UTAMA --}}
        @include('pengaturan.sidebar')

        {{-- 2. Area Form Profil & Password --}}
        <div class="md:col-span-3 bg-white rounded-[24px] border border-[#D4E8D4] shadow-sm overflow-hidden">
            
            {{-- Header Form --}}
            <div class="px-6 py-5 border-b border-[#D4E8D4] flex items-center gap-4">
                <div class="w-10 h-10 bg-green-50 text-[#2E7D32] rounded-xl flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1A2E1A] text-sm">Informasi Profil Akun Anda</h3>
                    <p class="text-[11px] text-gray-400">Perbarui data diri pribadi, alamat email, username, dan kata sandi berkala Anda.</p>
                </div>
            </div>

            {{-- Form Input Terintegrasi --}}
            <form action="{{ route('pengaturan.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- KELOMPOK 1: DATA PROFIL UTAMA --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Nama Lengkap --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', auth()->user()->nama ?? auth()->user()->name) }}" class="w-full px-4 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all" required>
                    </div>

                    {{-- Username --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Username Akun</label>
                        <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" class="w-full px-4 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all" required>
                    </div>

                    {{-- Alamat Email --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full px-4 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all" required>
                    </div>

                    {{-- No Telepon --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telepon" value="{{ old('no_telepon', auth()->user()->no_telepon) }}" class="w-full px-4 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all">
                    </div>
                </div>

                {{-- KELOMPOK 2: VALIDASI KEAMANAN & PASSWORD BARU --}}
                <div class="pt-4 border-t border-dashed border-gray-200 space-y-4">
                    <h4 class="text-xs font-bold text-gray-600 flex items-center gap-2">
                        <i class="fas fa-lock text-gray-400"></i> Pembaruan Kata Sandi (Kosongkan jika tidak diganti)
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Password Baru --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Kata Sandi Baru</label>
                            <div class="relative flex items-center">
                                <input type="password" id="password_baru" name="password_baru" class="w-full pl-4 pr-10 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all" placeholder="Minimal 6 karakter">
                                <button type="button" onclick="togglePasswordVisibility('password_baru', 'icon_baru')" class="absolute right-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i id="icon_baru" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Konfirmasi Kata Sandi Baru</label>
                            <div class="relative flex items-center">
                                <input type="password" id="password_baru_confirmation" name="password_baru_confirmation" class="w-full pl-4 pr-10 py-2.5 border border-[#D4E8D4] rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-[#2E7D32] focus:ring-1 focus:ring-[#2E7D32] transition-all" placeholder="Ketik ulang sandi baru">
                                <button type="button" onclick="togglePasswordVisibility('password_baru_confirmation', 'icon_konfirmasi')" class="absolute right-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i id="icon_konfirmasi" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KELOMPOK 3: PIN VERIFIKASI UTAMA --}}
                <div class="pt-4 border-t border-gray-100 bg-gray-50/50 p-4 rounded-2xl border border-gray-100 space-y-2">
                    <div class="space-y-1.5 max-w-sm">
                        <label class="text-[10px] font-black text-red-500 uppercase tracking-wider flex items-center gap-1">
                            <i class="fas fa-shield-alt"></i> Kata Sandi Saat Ini (Wajib diisi)
                        </label>
                        <div class="relative flex items-center">
                            <input type="password" id="password_sekarang" name="password_sekarang" class="w-full pl-4 pr-10 py-2.5 border border-red-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all" placeholder="Masukkan sandi Anda saat ini" required>
                            <button type="button" onclick="togglePasswordVisibility('password_sekarang', 'icon_sekarang')" class="absolute right-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i id="icon_sekarang" class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400">Diperlukan sebagai pin pengaman sebelum mengubah data profil.</p>
                    </div>
                </div>

                {{-- Baris Tombol Aksi --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 rounded-xl font-bold text-xs transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-[#2E7D32] hover:bg-[#1A5E20] text-white rounded-xl font-bold text-xs shadow-sm transition-all">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
</script>
@endsection