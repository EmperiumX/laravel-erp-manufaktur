<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERP New Citra Indonesia</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#fcfbfc] text-gray-800 min-h-screen flex flex-col justify-between items-center selection:bg-red-600 selection:text-white relative overflow-hidden font-sans w-full">
    
    <!-- Background Glow Elements (Light Theme) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-red-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-400/5 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-100/10 rounded-full blur-[150px]"></div>
    </div>

    <!-- Header Navigation -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center shadow-lg shadow-red-900/15">
                <i class="ri-instance-line text-xl text-amber-400"></i>
            </div>
            <span class="font-extrabold text-xl tracking-wider text-gray-900">NEW CITRA <span class="text-amber-600">ERP</span></span>
        </div>

        @if (Route::has('login'))
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white text-sm font-bold shadow-md shadow-red-900/10 transition-all duration-200 hover:scale-[1.03]">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-gray-600 hover:text-gray-900 text-sm font-semibold transition duration-150">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl border border-amber-500/30 hover:border-amber-500/60 text-amber-700 text-sm font-bold transition duration-150">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- Main Container representing the Bag Design -->
    <main class="flex-1 flex items-center justify-center px-6 py-8 z-10">
        <!-- Red Outer Container representing Red Bag body -->
        <div class="w-full max-w-3xl bg-gradient-to-b from-[#a81a1a] to-[#801010] p-3 sm:p-5 rounded-[32px] shadow-2xl shadow-red-950/20 relative overflow-hidden flex flex-col items-center">
            
            <!-- White Center Area representing White label area on the bag -->
            <div class="w-full bg-white rounded-[24px] p-8 sm:p-12 flex flex-col items-center text-center shadow-inner relative overflow-hidden">
                
                <!-- Gold Accent Top Line -->
                <div class="absolute top-0 left-0 w-full h-[4px] bg-gradient-to-r from-amber-400 via-yellow-500 to-amber-400"></div>

                <!-- Logo Bagian Tengah (Fish Logo from Bag) -->
                <div class="mb-4 flex flex-col items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo New Citra" class="h-28 object-contain">
                </div>

                <!-- Title -->
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 mb-3">
                    Enterprise Resource Planning
                </h1>

                <!-- Description -->
                <p class="text-gray-500 text-sm sm:text-base max-w-md mb-8 leading-relaxed">
                    Sistem pengelolaan manufaktur, kontrol persediaan gudang, penanganan penjualan konsinyasi, dan pelaporan keuangan terpadu New Citra Indonesia.
                </p>

                <!-- Actions inside White container -->
                <div class="flex flex-col sm:flex-row gap-4 w-full justify-center items-center max-w-md">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white font-bold shadow-lg shadow-red-900/15 transition-all duration-200 hover:scale-[1.03] text-center">
                            <i class="ri-dashboard-line mr-1.5"></i> Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white font-bold shadow-lg shadow-red-900/15 transition-all duration-200 hover:scale-[1.03] text-center">
                            <i class="ri-login-box-line mr-1.5 text-amber-300"></i> Masuk ke Sistem
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl border border-amber-500/40 hover:border-amber-500/60 text-amber-700 font-bold transition duration-200 text-center">
                                Daftar Akun Baru
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Red Banner/Footer representing the lower bottom red part of the bag -->
            <div class="w-full pt-4 pb-2 px-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-white/80 text-[11px] font-medium">
                <div class="flex items-center gap-2">
                    <i class="ri-whatsapp-line text-sm text-amber-400"></i>
                    <span>WA: 0858 6622 8323</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <i class="ri-instagram-line text-sm text-amber-400"></i>
                        <span>newcitra.indonesia</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i class="ri-global-line text-sm text-amber-400"></i>
                        <span>newcitraindonesia.id</span>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-6 text-center text-xs text-gray-500 z-10 border-t border-gray-100">
        <p>© {{ date('Y') }} New Citra Indonesia — Jl. Rogojembangan Barat 1 No.31, Semarang. All Rights Reserved.</p>
    </footer>

</body>
</html>
