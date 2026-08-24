# Malega Apparel Multi-Project Workspace

Workspace ini terdiri dari 2 project utama yang saling melengkapi:

1. **`backoffice/`** — Backoffice & Admin Management System dibangun dengan **Laravel 13.x**, **Livewire v4.x**, **Tailwind CSS**, dan database **MySQL**.
2. **`landingpage/`** — Landing Page marketing & katalog brand **Malega Apparel** dibangun dengan **Astro.js** dan **Tailwind CSS**.

---

## Panduan Menjalankan Project

### 1. Backoffice (Laravel 13 + Livewire v4 + Tailwind CSS)

Masuk ke direktori `backoffice`:
```bash
cd C:\laragon\www\malega-apparel\backoffice
```

**Konfigurasi Database MySQL**:
File `.env` sudah dikonfigurasi ke MySQL default Laragon:
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=malega_backoffice`
- `DB_USERNAME=root`
- `DB_PASSWORD=`

*Pastikan MySQL service di Laragon aktif. Jalankan migration jika database sudah dibuat:*
```bash
php artisan migrate
```

**Menjalankan Server Development**:
Buka 2 terminal (atau gunakan `composer run dev`):
```bash
# Terminal 1 - Backend Laravel Server
php artisan serve

# Terminal 2 - Vite Asset Bundler
npm run dev
```
Akses Backoffice di: `http://localhost:8000`

---

### 2. Landing Page (Astro.js + Tailwind CSS)

Masuk ke direktori `landingpage`:
```bash
cd C:\laragon\www\malega-apparel\landingpage
```

**Menjalankan Server Development**:
```bash
npm run dev
```
Akses Landing Page di: `http://localhost:4321`

**Build Production**:
```bash
npm run build
```

---

## Ringkasan Fitur yang Sudah Disediakan

### Backoffice:
- **Framework Core**: Laravel 13.26.1 dengan PHP 8.4.
- **Livewire v4.4**: Component-driven UI reaktif (`App\Livewire\Dashboard`).
- **Fitur Livewire**:
  - Dashboard KPI: Estimasi Pendapatan, Total Stok Gudang, Peringatan Stok Rendah, Pesanan Hari Ini.
  - Manajemen Katalog & Stok real-time: Pencarian live, filter kategori, penambahan stok inline (`+5`, `-1`).
  - Modal Reaktif: Tambah SKU produk baru langsung tanpa reload halaman.
  - Ringkasan Pesanan Pelanggan beserta status order (Paid, Packing, Shipped, Delivered).
- **Layout Terintegrasi**: `resources/views/layouts/app.blade.php` dengan `@livewireStyles`, `@livewireScripts`, dan dark theme modern.

### Landing Page (Astro.js):
- **Desain Streetwear Modern**: Dark aesthetic, glassmorphism, tipografi Plus Jakarta Sans & Space Grotesk.
- **Komponen Modular**:
  - `Navbar.astro`: Navigasi responsif dan tombol akses cepat ke Backoffice Portal.
  - `Hero.astro`: Headline bertenaga, badge rilis koleksi baru, dan statistik material 300GSM.
  - `FeaturedCollection.astro`: Grid produk apparel lengkap dengan harga, spesifikasi, dan badge status.
  - `BrandStory.astro`: Poin keunggulan craftsmanship dan kualitas bahan.
  - `Lookbook.astro`: Inspirasi gaya editorial bertema urban utility.
  - `Footer.astro`: Informasi brand, link media sosial, panduan, dan portal internal.
