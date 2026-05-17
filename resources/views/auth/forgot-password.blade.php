<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lupa Sandi — Apotek Citra Sehat</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        sora: ['Sora', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            touch-action: pan-x pan-y;
            background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans text-[#1A2E1A]">
    
    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
        <div class="w-20 h-20 bg-white/20 border border-white/30 rounded-[24px] flex items-center justify-center mb-6 backdrop-blur-md shadow-2xl">
            <svg class="w-10 h-10 fill-white" viewBox="0 0 24 24">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
            </svg>
        </div>
        <h1 class="font-sora text-3xl font-extrabold text-white mb-2 tracking-tight">CITRA SEHAT</h1>
        <p class="text-white/80 text-sm font-medium tracking-wide">Pemulihan Kata Sandi Sistem</p>
    </div>

    <div class="bg-white rounded-t-[40px] px-8 pt-10 pb-12 shadow-[0_-10px_40px_rgba(0,0,0,0.3)]">
        <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-8"></div> 
        <h2 class="font-sora text-2xl font-extrabold text-[#1A2E1A] mb-1">Minta Ubah Sandi</h2>
        <p class="text-[#7A8C7A] text-sm mb-8 font-medium">Masukkan email akun Anda untuk melakukan verifikasi</p>
        
        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf 

            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-xs font-bold border border-red-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-[11px] font-bold text-[#4A5C4A] mb-2 uppercase tracking-[0.15em]">Email Terdaftar</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#1A2E1A] font-semibold focus:border-[#2E7D32] focus:outline-none focus:ring-4 focus:ring-[#2E7D32]/5 transition-all outline-none"
                    placeholder="Contoh: admin@citrasehat.com">
            </div>

            <button type="submit" class="w-full h-16 bg-[#2E7D32] hover:bg-[#1B5E20] active:scale-[0.97] text-white font-bold text-lg rounded-2xl transition-all shadow-xl shadow-[#2E7D32]/30 mt-4 flex items-center justify-center gap-3 uppercase tracking-wider">
                Lanjut Verifikasi
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm font-bold text-[#7A8C7A] hover:text-[#2E7D32] transition-colors">Kembali ke Halaman Login</a>
            </div>
        </form>
    </div>

</body>
</html>