<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Malega Apparel — Backoffice Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          navy: { DEFAULT: '#0B132B', light: '#121B3D', lighter: '#182352' },
          gold: { DEFAULT: '#CBAC70', dark: '#BD9B58' },
          ivory: '#FDFCFF',
        },
        fontFamily: {
          display: ['"Playfair Display"', 'serif'],
          sans: ['Inter', 'sans-serif'],
          mono: ['"JetBrains Mono"', 'monospace'],
        },
      },
    },
  };
</script>
<style>
  body { background-color: #0B132B; }
  ::-webkit-scrollbar { width: 8px; height: 8px; }
  ::-webkit-scrollbar-track { background: #0B132B; }
  ::-webkit-scrollbar-thumb { background: #CBAC70; border-radius: 999px; opacity: .5; }

  /* garment-tag badge: hangtag shape with a punched hole */
  .tag-badge { position: relative; }
  .tag-badge::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 4px;
    border-radius: 999px;
    background: #0B132B;
    box-shadow: 0 0 0 1px currentColor;
  }

  .stitch { border-top: 1px dashed rgba(203,172,112,.35); }

  #sidebar { transition: transform .25s ease; }
  @media (max-width: 1023px) {
    #sidebar { position: fixed; inset: 0 auto 0 0; transform: translateX(-100%); z-index: 40; }
    #sidebar.open { transform: translateX(0); }
  }
</style>
</head>
<body class="font-sans text-ivory antialiased">

<div class="flex min-h-screen">

  <!-- Overlay (mobile) -->
  <div id="overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

  <!-- Sidebar -->
  <aside id="sidebar" class="w-64 shrink-0 bg-navy border-r border-gold/15 flex flex-col">
    <div class="h-20 flex flex-col items-center justify-center border-b border-gold/15">
      <p class="font-display text-xl tracking-[0.2em] text-ivory">MALEGA</p>
      <p class="text-[10px] tracking-[0.4em] text-gold mt-0.5">APPAREL</p>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
      <p class="px-3 mb-2 text-[10px] font-semibold tracking-[0.2em] text-ivory/40 uppercase">Menu Utama</p>

      <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md bg-gold/10 border-l-2 border-gold text-ivory">
        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12l1.5-1.5m0 0l6.75-6.75L18.75 10.5m-13.5 0v8.25a1.5 1.5 0 001.5 1.5h3.75v-6h3v6h3.75a1.5 1.5 0 001.5-1.5V10.5m-13.5 0l1.5-1.5"/></svg>
        <span class="text-sm font-medium">Dashboard</span>
      </a>
      <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
        <span class="text-sm">Produk</span>
      </a>
      <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.881-4.804 2.231-7.454a1.125 1.125 0 00-1.12-1.296H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
        <span class="text-sm">Pesanan</span>
        <span class="ml-auto text-[10px] bg-gold text-navy font-semibold px-1.5 py-0.5 rounded-full">12</span>
      </a>
      <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
        <span class="text-sm">Pelanggan</span>
      </a>
      <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 3.75c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
        <span class="text-sm">Inventori</span>
      </a>

      <p class="px-3 mt-6 mb-2 text-[10px] font-semibold tracking-[0.2em] text-ivory/40 uppercase">Lainnya</p>
      <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/></svg>
        <span class="text-sm">Laporan</span>
      </a>
      <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span class="text-sm">Pengaturan</span>
      </a>
    </nav>

    <div class="p-4 border-t border-gold/15">
      <div class="flex items-center gap-3 rounded-lg bg-white/5 px-3 py-2.5">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-navy font-semibold text-xs">AR</div>
        <div class="min-w-0">
          <p class="text-xs font-medium text-ivory truncate">Ahmad Rizal</p>
          <p class="text-[11px] text-ivory/40 truncate">Store Manager</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main -->
  <div class="flex-1 min-w-0 flex flex-col">

    <!-- Topbar -->
    <header class="h-20 sticky top-0 z-20 bg-navy/90 backdrop-blur border-b border-gold/15 flex items-center gap-4 px-4 lg:px-8">
      <button id="menuBtn" class="lg:hidden text-ivory/70">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
      </button>

      <div class="hidden md:block">
        <p class="text-[11px] text-ivory/40 tracking-wide">Overview / Dashboard</p>
      </div>

      <div class="flex-1 max-w-md ml-0 md:ml-6">
        <div class="relative">
          <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-ivory/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
          <input type="text" placeholder="Cari pesanan, produk, pelanggan..." class="w-full bg-white/5 border border-white/10 rounded-full py-2.5 pl-10 pr-4 text-sm text-ivory placeholder:text-ivory/30 focus:outline-none focus:border-gold/50">
        </div>
      </div>

      <div class="flex items-center gap-4 ml-auto">
        <button class="relative text-ivory/60 hover:text-ivory transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
          <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-gold"></span>
        </button>
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold to-gold-dark hidden sm:block"></div>
      </div>
    </header>

    <!-- Content -->
    <main class="flex-1 p-4 lg:p-8 space-y-6">

      <!-- Page header -->
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
          <h1 class="font-display text-3xl text-ivory">Dashboard</h1>
          <p class="text-sm text-ivory/40 mt-1">Ringkasan performa toko — 27 Agustus 2026</p>
        </div>
        <div class="flex items-center gap-2">
          <div class="flex items-center bg-white/5 border border-white/10 rounded-lg p-1 text-xs">
            <button class="px-3 py-1.5 rounded-md bg-gold text-navy font-semibold">7 Hari</button>
            <button class="px-3 py-1.5 rounded-md text-ivory/50">30 Hari</button>
            <button class="px-3 py-1.5 rounded-md text-ivory/50">1 Tahun</button>
          </div>
          <button class="flex items-center gap-2 bg-gold hover:bg-gold-dark transition-colors text-navy text-sm font-semibold px-4 py-2 rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            Ekspor
          </button>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-navy-light border border-gold/15 rounded-xl p-5">
          <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase">Total Pendapatan</p>
          <p class="font-display text-2xl lg:text-3xl text-ivory mt-2">Rp 284,5jt</p>
          <div class="flex items-center gap-2 mt-3">
            <span class="tag-badge text-emerald-400 border border-emerald-400/40 text-[11px] pl-4 pr-2 py-0.5 rounded-full">+12,4%</span>
            <span class="text-[11px] text-ivory/30">vs minggu lalu</span>
          </div>
        </div>

        <div class="bg-navy-light border border-gold/15 rounded-xl p-5">
          <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase">Pesanan Baru</p>
          <p class="font-display text-2xl lg:text-3xl text-ivory mt-2">1.248</p>
          <div class="flex items-center gap-2 mt-3">
            <span class="tag-badge text-emerald-400 border border-emerald-400/40 text-[11px] pl-4 pr-2 py-0.5 rounded-full">+5,1%</span>
            <span class="text-[11px] text-ivory/30">vs minggu lalu</span>
          </div>
        </div>

        <div class="bg-navy-light border border-gold/15 rounded-xl p-5">
          <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase">Pelanggan Baru</p>
          <p class="font-display text-2xl lg:text-3xl text-ivory mt-2">356</p>
          <div class="flex items-center gap-2 mt-3">
            <span class="tag-badge text-red-400 border border-red-400/40 text-[11px] pl-4 pr-2 py-0.5 rounded-full">-2,3%</span>
            <span class="text-[11px] text-ivory/30">vs minggu lalu</span>
          </div>
        </div>

        <div class="bg-navy-light border border-gold/15 rounded-xl p-5">
          <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase">Tingkat Konversi</p>
          <p class="font-display text-2xl lg:text-3xl text-ivory mt-2">3,82%</p>
          <div class="flex items-center gap-2 mt-3">
            <span class="tag-badge text-emerald-400 border border-emerald-400/40 text-[11px] pl-4 pr-2 py-0.5 rounded-full">+0,6%</span>
            <span class="text-[11px] text-ivory/30">vs minggu lalu</span>
          </div>
        </div>
      </div>

      <!-- Chart + Top products -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-navy-light border border-gold/15 rounded-xl p-6">
          <div class="flex items-start justify-between mb-6">
            <div>
              <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase">Performa 7 Hari Terakhir</p>
              <h2 class="font-display text-xl text-ivory mt-1">Tren Penjualan</h2>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-ivory/40">
              <span class="w-2 h-2 rounded-full bg-gold"></span> Pendapatan
            </div>
          </div>

          <svg viewBox="0 0 560 200" class="w-full h-48">
            <defs>
              <linearGradient id="salesFill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#CBAC70" stop-opacity="0.35"/>
                <stop offset="100%" stop-color="#CBAC70" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <g stroke="#FDFCFF" stroke-opacity="0.06">
              <line x1="0" y1="20" x2="560" y2="20"/>
              <line x1="0" y1="70" x2="560" y2="70"/>
              <line x1="0" y1="120" x2="560" y2="120"/>
              <line x1="0" y1="170" x2="560" y2="170"/>
            </g>
            <path d="M0,140 L80,110 L160,125 L240,80 L320,95 L400,50 L480,65 L560,30 L560,200 L0,200 Z" fill="url(#salesFill)"/>
            <path d="M0,140 L80,110 L160,125 L240,80 L320,95 L400,50 L480,65 L560,30" fill="none" stroke="#CBAC70" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <g fill="#CBAC70">
              <circle cx="0" cy="140" r="3.5"/>
              <circle cx="80" cy="110" r="3.5"/>
              <circle cx="160" cy="125" r="3.5"/>
              <circle cx="240" cy="80" r="3.5"/>
              <circle cx="320" cy="95" r="3.5"/>
              <circle cx="400" cy="50" r="3.5"/>
              <circle cx="480" cy="65" r="3.5"/>
              <circle cx="560" cy="30" r="4" stroke="#0B132B" stroke-width="2"/>
            </g>
          </svg>
          <div class="flex justify-between text-[11px] text-ivory/30 mt-2 px-1">
            <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
          </div>
        </div>

        <div class="bg-navy-light border border-gold/15 rounded-xl p-6">
          <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase">Terlaris Minggu Ini</p>
          <h2 class="font-display text-xl text-ivory mt-1 mb-4">Produk Unggulan</h2>

          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-navy text-xs font-bold">OS</div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-ivory truncate">Oxford Shirt — Navy</p>
                <p class="text-[11px] text-ivory/40">142 terjual</p>
              </div>
              <p class="text-sm font-mono text-gold">Rp 349rb</p>
            </div>
            <div class="stitch"></div>

            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-navy text-xs font-bold">TC</div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-ivory truncate">Tailored Chino</p>
                <p class="text-[11px] text-ivory/40">98 terjual</p>
              </div>
              <p class="text-sm font-mono text-gold">Rp 429rb</p>
            </div>
            <div class="stitch"></div>

            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-navy text-xs font-bold">LB</div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-ivory truncate">Leather Belt Classic</p>
                <p class="text-[11px] text-ivory/40">76 terjual</p>
              </div>
              <p class="text-sm font-mono text-gold">Rp 219rb</p>
            </div>
            <div class="stitch"></div>

            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-navy text-xs font-bold">WC</div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-ivory truncate">Wool Overcoat</p>
                <p class="text-[11px] text-ivory/40">54 terjual</p>
              </div>
              <p class="text-sm font-mono text-gold">Rp 1,2jt</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent orders -->
      <div class="bg-navy-light border border-gold/15 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between p-6 pb-4">
          <div>
            <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase">Aktivitas Terkini</p>
            <h2 class="font-display text-xl text-ivory mt-1">Pesanan Terbaru</h2>
          </div>
          <a href="#" class="text-sm text-gold hover:text-gold-dark transition-colors">Lihat Semua →</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-[11px] text-ivory/40 uppercase tracking-wide border-y border-white/5">
                <th class="px-6 py-3 font-medium">ID Pesanan</th>
                <th class="px-6 py-3 font-medium">Pelanggan</th>
                <th class="px-6 py-3 font-medium hidden md:table-cell">Produk</th>
                <th class="px-6 py-3 font-medium hidden sm:table-cell">Tanggal</th>
                <th class="px-6 py-3 font-medium">Total</th>
                <th class="px-6 py-3 font-medium">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr class="hover:bg-white/[.03] transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-ivory/70">#MLG-10245</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-semibold">RD</div>
                    <span>Rina Dewi</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-ivory/50 hidden md:table-cell">Oxford Shirt, Tailored Chino</td>
                <td class="px-6 py-4 text-ivory/50 hidden sm:table-cell">27 Ags 2026</td>
                <td class="px-6 py-4 font-mono text-gold">Rp 778rb</td>
                <td class="px-6 py-4">
                  <span class="tag-badge text-gold border border-gold/40 text-[11px] pl-4 pr-2 py-1 rounded-full">Dikirim</span>
                </td>
              </tr>
              <tr class="hover:bg-white/[.03] transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-ivory/70">#MLG-10244</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-semibold">BS</div>
                    <span>Budi Santoso</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-ivory/50 hidden md:table-cell">Wool Overcoat</td>
                <td class="px-6 py-4 text-ivory/50 hidden sm:table-cell">27 Ags 2026</td>
                <td class="px-6 py-4 font-mono text-gold">Rp 1,2jt</td>
                <td class="px-6 py-4">
                  <span class="tag-badge text-amber-300 border border-amber-300/40 text-[11px] pl-4 pr-2 py-1 rounded-full">Diproses</span>
                </td>
              </tr>
              <tr class="hover:bg-white/[.03] transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-ivory/70">#MLG-10243</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-semibold">SP</div>
                    <span>Sari Puspita</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-ivory/50 hidden md:table-cell">Leather Belt Classic</td>
                <td class="px-6 py-4 text-ivory/50 hidden sm:table-cell">26 Ags 2026</td>
                <td class="px-6 py-4 font-mono text-gold">Rp 219rb</td>
                <td class="px-6 py-4">
                  <span class="tag-badge text-emerald-400 border border-emerald-400/40 text-[11px] pl-4 pr-2 py-1 rounded-full">Selesai</span>
                </td>
              </tr>
              <tr class="hover:bg-white/[.03] transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-ivory/70">#MLG-10242</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-semibold">HN</div>
                    <span>Hendra Nugraha</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-ivory/50 hidden md:table-cell">Oxford Shirt — Navy</td>
                <td class="px-6 py-4 text-ivory/50 hidden sm:table-cell">26 Ags 2026</td>
                <td class="px-6 py-4 font-mono text-gold">Rp 349rb</td>
                <td class="px-6 py-4">
                  <span class="tag-badge text-red-400 border border-red-400/40 text-[11px] pl-4 pr-2 py-1 rounded-full">Dibatalkan</span>
                </td>
              </tr>
              <tr class="hover:bg-white/[.03] transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-ivory/70">#MLG-10241</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-semibold">DP</div>
                    <span>Dian Permata</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-ivory/50 hidden md:table-cell">Tailored Chino, Leather Belt</td>
                <td class="px-6 py-4 text-ivory/50 hidden sm:table-cell">25 Ags 2026</td>
                <td class="px-6 py-4 font-mono text-gold">Rp 648rb</td>
                <td class="px-6 py-4">
                  <span class="tag-badge text-gold border border-gold/40 text-[11px] pl-4 pr-2 py-1 rounded-full">Dikirim</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
  const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  function toggleSidebar() {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('hidden');
  }
  menuBtn.addEventListener('click', toggleSidebar);
  overlay.addEventListener('click', toggleSidebar);
</script>

</body>
</html>