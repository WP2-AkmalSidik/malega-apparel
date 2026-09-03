'use client';

import React, { useState, useMemo, Suspense } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { 
  Search, 
  SlidersHorizontal, 
  ArrowLeft,
  ArrowRight,
  Sparkles,
  Layers,
  Flame,
  Check,
  X,
  ArrowUpDown,
  ShoppingBag
} from 'lucide-react';
import { productsCatalog } from '../../data/products';
import ProductCard from '../../components/ProductCard';

function ProductsCatalogContent() {
  const searchParams = useSearchParams();
  const router = useRouter();
  
  const categoryParam = searchParams.get('category');
  const sortParam = searchParams.get('sort');
  const searchParam = searchParams.get('search');

  const [activeCategory, setActiveCategory] = useState<string>(categoryParam || 'all');
  const [searchQuery, setSearchQuery] = useState<string>(searchParam || '');
  const [sortBy, setSortBy] = useState<string>(sortParam || 'featured');
  const [selectedSize, setSelectedSize] = useState<string>('all');
  const [selectedGsm, setSelectedGsm] = useState<string>('all');

  const categoryOptions = [
    { id: 'all', label: 'Semua Artikel' },
    { id: 'T-Shirts', label: 'T-Shirts (300GSM)' },
    { id: 'Outerwear', label: 'Hoodies & Outerwear' },
    { id: 'Bottoms', label: 'Cargo & Selvedge' },
    { id: 'Accessories', label: 'Aksesori & Caps' },
  ];

  const filteredProducts = useMemo(() => {
    return productsCatalog.filter(p => {
      // Category
      if (activeCategory !== 'all' && p.category !== activeCategory) {
        return false;
      }
      // GSM
      if (selectedGsm === '300' && p.gsm !== 300) return false;
      if (selectedGsm === '380' && p.gsm !== 380) return false;

      // Size
      if (selectedSize !== 'all' && !p.sizes.includes(selectedSize)) {
        return false;
      }

      // Search
      if (searchQuery.trim() !== '') {
        const q = searchQuery.toLowerCase();
        const matchTitle = p.title.toLowerCase().includes(q);
        const matchDesc = p.description.toLowerCase().includes(q);
        const matchMat = p.material.toLowerCase().includes(q);
        const matchSub = p.subtitle.toLowerCase().includes(q);
        if (!matchTitle && !matchDesc && !matchMat && !matchSub) return false;
      }

      return true;
    }).sort((a, b) => {
      if (sortBy === 'price_asc') return a.price - b.price;
      if (sortBy === 'price_desc') return b.price - a.price;
      if (sortBy === 'sold') return b.soldCount - a.soldCount;
      if (sortBy === 'rating') return b.rating - a.rating;
      return 0; // default featured
    });
  }, [activeCategory, selectedGsm, selectedSize, searchQuery, sortBy]);

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-6">
      
      {/* 1. Header Banner */}
      <div className="rounded-3xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-6 sm:p-8 border border-[#CBAC70]/30 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div className="space-y-1.5">
          <div className="flex items-center gap-2">
            <ShoppingBag className="w-4 h-4 text-[#CBAC70]" />
            <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-[#CBAC70]">
              KATALOG BELANJA LENGKAP
            </span>
          </div>
          <h1 className="text-2xl sm:text-3xl font-black text-[#FDFCFF] uppercase tracking-wide">
            Semua Produk & Rilisan Pakaian
          </h1>
          <p className="text-xs sm:text-sm text-[#94A3B8] max-w-xl leading-relaxed">
            Jelajahi seluruh koleksi esensial streetwear Malega Apparel. Pilih varian warna, ukuran, dan spesifikasi favorit Anda.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <Link
            href="/katalog"
            className="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-[#CBAC70]/40 text-[#CBAC70] text-xs font-bold transition flex items-center gap-2 shadow"
          >
            <Layers className="w-4 h-4" />
            <span>Lihat Lookbook Koleksi →</span>
          </Link>
        </div>
      </div>

      {/* 2. Control Bar (Categories, Search, Filters, Sort) */}
      <div className="space-y-3">
        {/* Category Tabs */}
        <div className="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
          {categoryOptions.map(cat => (
            <button
              type="button"
              key={cat.id}
              onClick={() => setActiveCategory(cat.id)}
              className={`px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap cursor-pointer ${
                activeCategory === cat.id
                  ? 'bg-gradient-to-r from-[#CBAC70] to-[#A58645] text-[#0B132B] shadow-md font-black scale-102'
                  : 'bg-[#0E1736] text-slate-300 hover:text-white border border-white/5 hover:border-white/20'
              }`}
            >
              {cat.label}
            </button>
          ))}
        </div>

        {/* Secondary Filter & Search Bar */}
        <div className="p-3 sm:p-4 rounded-2xl bg-[#0E1736] border border-white/10 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
          {/* Search Input */}
          <div className="relative flex-1">
            <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari kaos 300GSM, hoodie fleece, kargo ripstop..."
              className="w-full bg-[#070D1F] border border-white/10 rounded-xl py-2 pl-9 pr-8 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70] transition"
            />
            {searchQuery && (
              <button
                type="button"
                onClick={() => setSearchQuery('')}
                className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>

          {/* Size Filter Dropdown */}
          <div className="flex items-center gap-2">
            <select
              value={selectedSize}
              onChange={(e) => setSelectedSize(e.target.value)}
              className="bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-[#CBAC70] cursor-pointer"
            >
              <option value="all">Semua Ukuran</option>
              <option value="S">Size S</option>
              <option value="M">Size M</option>
              <option value="L">Size L</option>
              <option value="XL">Size XL</option>
              <option value="XXL">Size XXL</option>
            </select>

            {/* Sort Dropdown */}
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-[#CBAC70] cursor-pointer"
            >
              <option value="featured">Rekomendasi Utama</option>
              <option value="sold">Terlaris (Best Seller)</option>
              <option value="rating">Rating Tertinggi (4.9★)</option>
              <option value="price_asc">Harga Terendah</option>
              <option value="price_desc">Harga Tertinggi</option>
            </select>
          </div>
        </div>
      </div>

      {/* 3. Product Grid */}
      {filteredProducts.length > 0 ? (
        <div className="space-y-3">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono px-1">
            <span>Menampilkan {filteredProducts.length} artikel pakaian</span>
            <span className="text-[#CBAC70]">Semua Produk 100% Original Bespoke</span>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            {filteredProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </div>
      ) : (
        <div className="py-20 text-center rounded-3xl bg-[#0E1736] border border-white/10 p-8 space-y-3">
          <p className="text-base font-bold text-slate-200">Tidak ada produk yang sesuai dengan filter Anda</p>
          <p className="text-xs text-slate-500">Coba reset kata kunci pencarian atau pilih kategori lain.</p>
          <button
            type="button"
            onClick={() => {
              setActiveCategory('all');
              setSearchQuery('');
              setSelectedSize('all');
            }}
            className="px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs shadow hover:bg-[#E3CD99] transition cursor-pointer"
          >
            Reset Filter
          </button>
        </div>
      )}

    </div>
  );
}

export default function ProductsPage() {
  return (
    <Suspense fallback={<div className="py-20 text-center text-xs font-mono text-slate-400">Memuat katalog produk...</div>}>
      <ProductsCatalogContent />
    </Suspense>
  );
}
