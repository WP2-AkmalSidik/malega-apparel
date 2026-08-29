<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Malega Apparel — Backoffice' }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-[#070C1A] text-slate-100 min-h-screen antialiased selection:bg-[#CBAC70] selection:text-[#0B132B] font-sans flex flex-col justify-between">
        <!-- Ambient Brand Glow Effects -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-[#CBAC70]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-[#0B132B] rounded-full blur-3xl opacity-60"></div>
            <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-[#1C2541]/40 rounded-full blur-3xl"></div>
        </div>

        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

        <footer class="py-4 text-center text-xs text-slate-500 border-t border-slate-800/60 bg-[#0B132B]/40 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} Malega Apparel. All rights reserved.</span>
                <span class="font-mono text-[11px] text-slate-400">Backoffice Engine v1.0.0 &bull; Modular Monolith</span>
            </div>
        </footer>

        <!-- Modern Toast Notifications in Bottom-Right Corner -->
        <x-toast />

        @livewireScripts
    </body>
</html>
