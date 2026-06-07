<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk — Apotek Citra Sehat</title>
    
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
        /* Mengunci tampilan agar tidak bisa di-zoom manual di HP agar terasa seperti Aplikasi Native */
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
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
            </svg>
        </div>
        <h1 class="font-sora text-3xl font-extrabold text-white mb-2 tracking-tight">CITRA SEHAT</h1>
        <p class="text-white/80 text-sm font-medium tracking-wide">Sistem Informasi Manajemen Apotek</p>
    </div>

    <div class="bg-white rounded-t-[40px] px-8 pt-10 pb-12 shadow-[0_-10px_40px_rgba(0,0,0,0.3)]">
        <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-8"></div> 
        <h2 class="font-sora text-2xl font-extrabold text-[#1A2E1A] mb-1">Masuk</h2>
        <p class="text-[#7A8C7A] text-sm mb-8 font-medium">Silakan masuk untuk mengelola apotek</p>
        
        @if (session('status'))
            <div class="bg-green-50 text-green-600 p-4 rounded-2xl text-xs font-bold border border-green-100 flex items-center gap-2 mb-5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf 
            
            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-xs font-bold border border-red-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-[11px] font-bold text-[#4A5C4A] mb-2 uppercase tracking-[0.15em]">Username / Email</label>
                {{-- 🌟 FIX: Atribut name diubah menjadi 'email' agar lolos validasi controller hibrida --}}
                <input type="text" name="email" value="{{ old('email') }}" required 
                    class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#1A2E1A] font-semibold focus:border-[#2E7D32] focus:outline-none focus:ring-4 focus:ring-[#2E7D32]/5 transition-all outline-none"
                    placeholder="Contoh: admin">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-[#4A5C4A] mb-2 uppercase tracking-[0.15em]">Password</label>
                <input type="password" name="password" required 
                    class="w-full h-14 px-5 border-2 border-[#E8F5E9] rounded-2xl bg-[#FAFFF9] text-[#1A2E1A] font-semibold focus:border-[#2E7D32] focus:outline-none focus:ring-4 focus:ring-[#2E7D32]/5 transition-all outline-none"
                    placeholder="••••••••">
            </div>
            
            <div class="flex items-center justify-between py-2">
                <label class="flex items-center gap-3 text-sm text-[#7A8C7A] font-semibold cursor-pointer">
                    <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-[#D4E8D4] text-[#2E7D32] focus:ring-[#2E7D32]">
                    Ingat Saya
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-bold text-[#2E7D32] hover:underline">Lupa Sandi?</a>
            </div>

            <button type="submit" class="w-full h-16 bg-[#2E7D32] hover:bg-[#1B5E20] active:scale-[0.97] text-white font-bold text-lg rounded-2xl transition-all shadow-xl shadow-[#2E7D32]/30 mt-4 flex items-center justify-center gap-3 uppercase tracking-wider">
                Masuk Ke Sistem
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </form>
    </div>

</body>
</html>