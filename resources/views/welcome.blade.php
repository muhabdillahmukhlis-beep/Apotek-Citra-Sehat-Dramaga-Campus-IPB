<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — Apotek Citra Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center">
    <div class="text-center">
        <div class="bg-green-600 w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-green-200">
            <svg class="w-12 h-12 fill-white" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
        </div>
        <h1 class="text-4xl font-black text-gray-800 font-['Sora'] mb-2">APOTEK CITRA SEHAT</h1>
        <p class="text-gray-500 mb-8 text-lg">Sistem Informasi Manajemen Apotek Terpadu</p>
        
        <div class="flex gap-4 justify-center">
            <a href="{{ route('login') }}" class="bg-green-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-green-700 transition transform hover:-translate-y-1 shadow-xl shadow-green-100">
                MASUK KE SISTEM
            </a>
        </div>
    </div>
    
    <p class="mt-20 text-gray-400 text-sm font-medium uppercase tracking-widest">&copy; 2024 Citra Sehat Team</p>
</body>
</html>