<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Lacak Pengiriman Pesanan | Malega Apparel' }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-[#070C1A] text-slate-100 min-h-screen antialiased selection:bg-[#CBAC70] selection:text-[#0B132B] font-sans flex flex-col justify-between">
        <!-- Ambient Brand Glow Effects -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-[#CBAC70]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-[#0B132B] rounded-full blur-3xl opacity-60"></div>
            <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-[#1C2541]/40 rounded-full blur-3xl"></div>
        </div>

        <!-- Top Navigation Header -->
        <header class="sticky top-0 z-30 bg-[#0B132B]/80 backdrop-blur-md border-b border-slate-800/80">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#CBAC70] via-[#DFB67A] to-[#CBAC70] p-0.5 shadow-lg shadow-[#CBAC70]/20 flex items-center justify-center">
                        <div class="w-full h-full bg-[#070C1A] rounded-[10px] flex items-center justify-center">
                            <span class="font-display font-bold text-base text-[#CBAC70] tracking-wider">M</span>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display tracking-[0.25em] text-sm text-slate-100 font-bold group-hover:text-[#CBAC70] transition-colors">MALEGA</span>
                        <span class="text-[9px] font-mono tracking-[0.3em] text-[#CBAC70] -mt-1 font-semibold">APPAREL</span>
                    </div>
                </a>

                <!-- Right Links -->
                <div class="flex items-center gap-3">
                    <a
                        href="https://wa.me/6281234567890?text=Halo%20Malega%20Apparel,%20saya%20ingin%20menanyakan%20status%20pesanan"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs font-semibold transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Bantuan CS</span>
                    </a>

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="px-3.5 py-1.5 rounded-xl bg-[#CBAC70] hover:bg-[#DFB67A] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-colors"
                        >
                            Dashboard Admin
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="px-3.5 py-1.5 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors"
                        >
                            Masuk Staf
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-6xl w-full mx-auto p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="py-6 text-center text-xs text-slate-500 border-t border-slate-800/60 bg-[#0B132B]/40 backdrop-blur-md">
            <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="font-display font-bold text-slate-300">MALEGA APPAREL</span>
                    <span>&bull;</span>
                    <span>Live Logistics Tracking Portal</span>
                </div>
                <span class="font-mono text-[11px] text-slate-400">Powered by Biteship Logistics Engine &bull; Enterprise Grade</span>
            </div>
        </footer>

        <!-- Toast Notifications -->
        <x-toast />

        @livewireScripts
    </body>
</html>
