@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F0F4F0] pb-24 relative">
    
    {{-- HEADER HALAMAN --}}
    <div class="bg-white px-6 py-5 border-b border-[#D4E8D4] flex items-center justify-between sticky top-0 z-40">
        <div>
            <h1 class="font-sora font-extrabold text-[#1A2E1A] text-lg leading-none">Manajemen Pengguna</h1>
            <p class="text-[10px] font-bold text-[#7A8C7A] tracking-widest uppercase mt-1">Kelola akun dan hak akses pengguna sistem — Admin Only</p>
        </div>
        <div class="relative w-80">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" placeholder="Cari obat, transaksi, laporan..." 
                   class="w-full h-10 pl-10 pr-4 bg-[#F0F4F0] rounded-full text-xs font-medium border-none outline-none focus:ring-1 focus:ring-[#2E7D32]">
        </div>
    </div>

    <div class="p-6 grid grid-cols-1 xl:grid-cols-12 gap-6 items-start max-w-[1600px] mx-auto">
        
        {{-- TABEL DAFTAR PENGGUNA --}}
        <div class="xl:col-span-7 bg-white rounded-[24px] border border-[#D4E8D4] p-6 shadow-sm">
            
            {{-- Flash Message Success --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-xl flex items-center gap-2 animate-fade-in">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Flash Message Error Validasi --}}
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl">
                    <div class="font-bold mb-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> Tindakan Gagal:</div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <h2 class="font-sora font-bold text-[#1A2E1A] text-sm">Daftar Pengguna</h2>
                <button onclick="bukaModalUser()" class="h-9 px-4 bg-[#2E7D32] hover:bg-[#1B5E20] text-white text-xs font-bold rounded-xl flex items-center gap-2 transition-all shadow-sm">
                    <i class="fas fa-plus text-[10px]"></i> Tambah User
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#F0F4F0] text-[10px] font-bold text-[#7A8C7A] uppercase tracking-wider">
                            <th class="pb-3 pl-2">Nama Lengkap</th>
                            <th class="pb-3">Username</th>
                            <th class="pb-3 text-center">Role</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-center">Aksi</th>
                            <th class="pb-3 pr-2 text-right">Login Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F9FBF9] text-xs">
                        @forelse($users as $user)
                        <tr class="hover:bg-[#F9FBF9] transition-colors">
                            <td class="py-4 pl-2 font-bold text-[#1A2E1A] max-w-[180px] break-words">{{ $user->nama }}</td>
                            <td class="py-4 font-mono font-bold text-[#7A8C7A]">{{ $user->username }}</td>
                            <td class="py-4 text-center">
                                @php
                                    $roleClasses = [
                                        'admin'    => 'bg-blue-50 text-blue-600',
                                        'apoteker' => 'bg-purple-50 text-purple-600',
                                        'kasir'    => 'bg-emerald-50 text-emerald-600',
                                        'pemilik'  => 'bg-orange-50 text-orange-600'
                                    ];
                                    $class = $roleClasses[strtolower($user->role)] ?? 'bg-gray-50 text-gray-600';
                                @endphp
                                {{-- PERBAIKAN: Menggunakan properti database asli ($user->role) --}}
                                <span class="px-3 py-1 rounded-full font-bold text-[10px] capitalize {{ $class }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                @if($user->is_aktif)
                                    <span class="px-3 py-1 rounded-full font-bold text-[10px] bg-green-50 text-green-600">Aktif</span>
                                @else
                                    <span class="px-3 py-1 rounded-full font-bold text-[10px] bg-gray-100 text-gray-500">Nonaktif</span>
                                @endif
                            </td>

                            {{-- KELOMPOK TOMBOL AKSI --}}
                            <td class="py-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Form Toggle Status Aktif/Nonaktif --}}
                                    <form action="{{ route('user.status', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->is_aktif)
                                            <button type="submit" title="Nonaktifkan User" class="w-7 h-7 bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                <i class="fas fa-ban text-[11px]"></i>
                                            </button>
                                        @else
                                            <button type="submit" title="Aktifkan User" class="w-7 h-7 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                <i class="fas fa-check text-[11px]"></i>
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Form Hapus Akun Permanen --}}
                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->nama }} secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus User" class="w-7 h-7 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                            <i class="fas fa-trash text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td class="py-4 pr-2 text-right font-medium text-[#7A8C7A]">
                                {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m H:i') : 'Belum Pernah' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            {{-- PERBAIKAN: Colspan disesuaikan menjadi 6 sesuai total kolom di header --}}
                            <td colspan="6" class="py-8 text-center text-gray-400 font-medium">Belum ada data pengguna.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MATRIKS HAK AKSES STABIL --}}
        <div class="xl:col-span-5 bg-white rounded-[24px] border border-[#D4E8D4] p-6 shadow-sm">
            <div class="mb-6">
                <h2 class="font-sora font-bold text-[#1A2E1A] text-sm">Matriks Hak Akses</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse">
                    <thead>
                        <tr class="border-b border-[#F0F4F0] text-[10px] font-bold text-[#7A8C7A] uppercase tracking-wider">
                            <th class="pb-3 text-left pl-2">Fitur Sistem</th>
                            <th class="pb-3">Admin</th>
                            <th class="pb-3">Kasir</th>
                            <th class="pb-3">Apoteker</th>
                            <th class="pb-3 pr-2">Pemilik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F9FBF9] text-xs font-bold">
                        @foreach($matriksHakAkses as $row)
                        <tr class="hover:bg-[#F9FBF9] transition-colors">
                            <td class="py-4 text-left pl-2 font-semibold text-[#1A2E1A]">{{ $row['fitur'] }}</td>
                            
                            <td class="py-4">
                                <div class="flex justify-center">
                                    @if($row['admin'])
                                        <span class="w-5 h-5 bg-[#2E7D32] text-white rounded-md flex items-center justify-center text-[10px] shadow-sm"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="w-5 h-5 border border-gray-200 rounded-md block"></span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4">
                                <div class="flex justify-center">
                                    @if($row['kasir'])
                                        <span class="w-5 h-5 bg-[#2E7D32] text-white rounded-md flex items-center justify-center text-[10px] shadow-sm"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="w-5 h-5 border border-gray-200 rounded-md block"></span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4">
                                <div class="flex justify-center">
                                    @if($row['apoteker'])
                                        <span class="w-5 h-5 bg-[#2E7D32] text-white rounded-md flex items-center justify-center text-[10px] shadow-sm"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="w-5 h-5 border border-gray-200 rounded-md block"></span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 pr-2">
                                <div class="flex justify-center">
                                    @if($row['pemilik'])
                                        <span class="w-5 h-5 bg-[#2E7D32] text-white rounded-md flex items-center justify-center text-[10px] shadow-sm"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="w-5 h-5 border border-gray-200 rounded-md block"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL REGISTRASI USER BARU --}}
    <div id="modalUserbaru" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-[28px] border border-[#D4E8D4] w-full max-w-lg p-6 shadow-xl mx-4 transform scale-95 transition-transform duration-300" id="modalContent">
            
            <div class="flex items-center justify-between mb-5 border-b border-[#F0F4F0] pb-3">
                <h3 class="font-sora font-extrabold text-[#1A2E1A] text-sm">Registrasi Pengguna Baru</h3>
                <button onclick="tutupModalUser()" class="w-8 h-8 rounded-full bg-[#F0F4F0] hover:bg-red-50 hover:text-red-600 text-gray-500 transition-colors flex items-center justify-center text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('user.store') }}" method="POST" class="space-y-4 text-xs font-semibold text-[#1A2E1A]">
                @csrf
                <div>
                    <label class="block text-[10px] uppercase font-bold text-[#7A8C7A] mb-1">Nama Lengkap & Gelar</label>
                    <input type="text" name="nama" required placeholder="Contoh: Rina Kartika, S.Farm." 
                           class="w-full h-10 px-4 bg-[#F0F4F0] rounded-xl border-none outline-none focus:ring-1 focus:ring-[#2E7D32]">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-[#7A8C7A] mb-1">Username</label>
                        <input type="text" name="username" required placeholder="rina.kasir" 
                               class="w-full h-10 px-4 bg-[#F0F4F0] rounded-xl border-none outline-none focus:ring-1 focus:ring-[#2E7D32]">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-[#7A8C7A] mb-1">Hak Akses (Role)</label>
                        <select name="role" required class="w-full h-10 px-4 bg-[#F0F4F0] rounded-xl border-none outline-none focus:ring-1 focus:ring-[#2E7D32] font-semibold text-xs appearance-none">
                            <option value="kasir">Kasir</option>
                            <option value="apoteker">Apoteker</option>
                            <option value="admin">Admin</option>
                            <option value="pemilik">Pemilik Apotek</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold text-[#7A8C7A] mb-1">Alamat Email Resmi</label>
                    <input type="email" name="email" required placeholder="rina@apotek.com" 
                           class="w-full h-10 px-4 bg-[#F0F4F0] rounded-xl border-none outline-none focus:ring-1 focus:ring-[#2E7D32]">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-[#7A8C7A] mb-1">Kata Sandi Awal</label>
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full h-10 px-4 bg-[#F0F4F0] rounded-xl border-none outline-none focus:ring-1 focus:ring-[#2E7D32]">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-[#7A8C7A] mb-1">No. Telepon / WA</label>
                        <input type="text" name="no_telepon" placeholder="08xxxxxxxxxx" 
                               class="w-full h-10 px-4 bg-[#F0F4F0] rounded-xl border-none outline-none focus:ring-1 focus:ring-[#2E7D32]">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#F0F4F0] mt-6">
                    <button type="button" onclick="tutupModalUser()" class="h-10 px-5 bg-[#F0F4F0] hover:bg-gray-200 text-[#7A8C7A] font-bold rounded-xl transition-all">
                        Batal
                    </button>
                    <button type="submit" class="h-10 px-6 bg-[#2E7D32] hover:bg-[#1B5E20] text-white font-bold rounded-xl transition-all shadow-sm">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function bukaModalUser() {
        const modal = document.getElementById('modalUserbaru');
        const content = document.getElementById('modalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 20);
    }

    function tutupModalUser() {
        const modal = document.getElementById('modalUserbaru');
        const content = document.getElementById('modalContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalUserbaru');
        if (event.target == modal) {
            tutupModalUser();
        }
    }
</script>
@endsection