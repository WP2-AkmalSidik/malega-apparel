# Malega Apparel Multi-Project Workspace

Workspace ini terdiri dari 3 project enterprise yang saling terintegrasi:

1. **`backoffice/`** — Backoffice & Admin Inventory Management System (**Laravel 13.x** + **Livewire v4.x** + **Tailwind CSS** + **MySQL**).
2. **`landingpage/`** — Brand Story & High-Performance Editorial Landing Page (**Astro.js** + **Tailwind CSS**).
3. **`storefront/`** — Bespoke Luxury Brand E-Commerce & Ordering Storefront (**Next.js 16+ App Router** + **TypeScript** + **Tailwind CSS**) dengan identitas resmi:
   - **Brand Gold Accent**: `#CBAC70`
   - **Text Color**: `#FDFCFF`
   - **Background**: `#0B132B`
   - **Typography**: `Plus Jakarta Sans`

---

## Panduan Menjalankan Project

### 1. Storefront E-Commerce (Next.js 16 + TypeScript + Tailwind)
```bash
cd C:\laragon\www\malega-apparel\storefront
npm run dev
```
> **URL Toko E-Commerce:** `http://localhost:3000`

**Halaman & Fitur Toko:**
- **`/` (Home)**: Hero section mewah, capsule drops SS26, kategori produk, craftsmanship 300GSM, dan VIP membership banner.
- **`/products` (Catalog)**: Halaman katalog lengkap dengan filter kategori (*T-Shirts, Outerwear, Bottoms, Accessories*), filter ukuran (*S-XXL / 28-36*), sortir harga & terlaris, serta search bar live.
- **`/products/[id]` (Product Detail)**: Galeri foto produk, selector warna (*colorways*), selector ukuran dengan modal **Size Chart Guide**, rincian gramasi kain (*300GSM Heavy Cotton*), ulasan pembeli, dan tombol **Instant Checkout**.
- **`/checkout` (Enterprise Checkout)**: Form alamat penerima, pilihan kurir logistik (*SPX Express, J&T, SiCepat, Instant*), input voucher diskon (*MALEGAVIP15*, *FREESHIPXTRA*), gateway pembayaran interaktif (*QRIS, Virtual Account BCA/Mandiri, Kartu Kredit, COD*).
- **`/order-confirmation` (Invoice & Tracking)**: Nota resmi dengan nomor invoice, nomor resi kurir, tracking status timeline interaktif, dan tombol konfirmasi otomatis via WhatsApp.

---

### 2. Backoffice Admin (Laravel 13 + Livewire v4 + MySQL)
```bash
cd C:\laragon\www\malega-apparel\backoffice
php artisan serve
```
*(Buka terminal kedua jika ingin menjalankan Vite hot-reload):*
```bash
npm run dev
```
> **URL Backoffice:** `http://localhost:8000`

---

### 3. Editorial Landing Page (Astro.js + Tailwind CSS)
```bash
cd C:\laragon\www\malega-apparel\landingpage
npm run dev
```
> **URL Landing Page:** `http://localhost:4321`
