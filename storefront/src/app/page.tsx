'use client';

import React, { useState, useMemo } from 'react';
import { 
  Search, 
  SlidersHorizontal, 
  Sparkles, 
  Tag, 
  RotateCcw, 
  Flame, 
  Check, 
  ShoppingBag,
  ArrowRight
} from 'lucide-react';
import { productsCatalog } from '../data/products';
import ProductCard from '../components/ProductCard';

export default function StoreHomePage() {
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedSize, setSelectedSize] = useState<string>('all');
  const [sortBy, setSortBy] = useState<string>('featured');
  const [onlyNewDrops, setOnlyNewDrops] = useState<boolean>(false);
  const [onlyBestSellers, setOnlyBestSellers] = useState<boolean>(false);

  const categories = [
    { id: 'all', label: 'Semua Koleksi' },
    { id: 'T-Shirts', label: 'Heavyweight Tees (300GSM)' },
    { id: 'Outerwear', label: 'Hoodies & Outer' },
    { id: 'Bottoms', label: 'Cargo & Denim' },
    { id: 'Accessories', label: 'Caps & Bags' }
  ];

  const sizeOptions = ['all', 'S', 'M', 'L', 'XL', 'XXL', '28', '30', '32', '34', '36'];

  const filteredProducts = useMemo(() => {
    return productsCatalog.filter(p => {
      // Category filter
      if (selectedCategory !== 'all' && p.category !== selectedCategory) {
        return false;
      }
      // Search filter
      if (searchQuery.trim() !== '') {
        const q = searchQuery.toLowerCase();
        const matchTitle = p.title.toLowerCase().includes(q);
        const matchSubtitle = p.subtitle.toLowerCase().includes(q);
        const matchDesc = p.description.toLowerCase().includes(q);
        const matchCat = p.category.toLowerCase().includes(q);
        const matchMaterial = p.material.toLowerCase().includes(q);
        if (!matchTitle && !matchSubtitle && !matchDesc && !matchCat && !matchMaterial) return false;
      }
      // Size filter
      if (selectedSize !== 'all' && !p.sizes.includes(selectedSize)) {
        return false;
      }
      // New drops
      if (onlyNewDrops && !p.isNewDrop) {
        return false;
      }
      // Best sellers
      if (onlyBestSellers && !p.isBestSeller) {
        return false;
      }
      return true;
    }).sort((a, b) => {
      if (sortBy === 'price-low') return a.price - b.price;
      if (sortBy === 'price-high') return b.price - a.price;
      if (sortBy === 'sold') return b.soldCount - a.soldCount;
      if (sortBy === 'rating') return b.rating - a.rating;
      return 0; // default featured
    });
  }, [selectedCategory, searchQuery, selectedSize, onlyNewDrops, onlyBestSellers, sortBy]);

  const handleResetFilters = () => {
    setSelectedCategory('all');
    setSearchQuery('');
    setSelectedSize('all');
    setSortBy('featured');
    setOnlyNewDrops(false);
    setOnlyBestSellers(false);
  };

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-5">
      
      {/* 1. Minimal Luxury Promo Strip */}
      <div className="rounded-xl sm:rounded-2xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#14204A] border border-[#CBAC70]/30 px-3.5 py-2.5 sm:px-5 sm:py-3 flex items-center justify-between shadow-md">
        <div className="flex items-center gap-2 text-xs min-w-0">
          <Tag className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#CBAC70] shrink-0" />
          <span className="text-[#94A3B8] text-[11px] sm:text-xs truncate">
            Gunakan voucher VIP: <strong className="text-[#CBAC70] font-mono">MALEGAVIP15</strong> (Diskon 15%)
          </span>
        </div>
        <span className="text-[10px] sm:text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full whitespace-nowrap hidden xs:inline-block">
          Gratis Ongkir Active
        </span>
      </div>

      {/* 2. Direct Category Filter Tabs (Scrollable on Mobile) */}
      <div className="overflow-x-auto scrollbar-none -mx-3 px-3 sm:mx-0 sm:px-0">
        <div className="flex items-center gap-2 min-w-max pb-1">
          {categories.map((cat) => {
            const count = cat.id === 'all' 
              ? productsCatalog.length 
              : productsCatalog.filter(p => p.category === cat.id).length;
            const isSelected = selectedCategory === cat.id;

            return (
              <button
                key={cat.id}
                onClick={() => setSelectedCategory(cat.id)}
                className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 active:scale-95 ${
                  isSelected
                    ? 'bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] shadow-md'
                    : 'bg-[#0E1736] hover:bg-[#14204A] text-[#94A3B8] hover:text-[#FDFCFF] border border-white/10 hover:border-[#CBAC70]/40'
                }`}
              >
                <span>{cat.label}</span>
                <span className={`text-[10px] px-1.5 py-0.2 rounded-full ${isSelected ? 'bg-[#0B132B] text-[#CBAC70]' : 'bg-[#070D1F] text-[#94A3B8]'}`}>
                  {count}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      {/* 3. Search & Control Toolbar */}
      <div className="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-[#0E1736] border border-[#CBAC70]/20 space-y-3 shadow-md">
        
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          
          {/* Search Bar Input */}
          <div className="relative flex-1">
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari kaos boxy 300GSM, hoodie, cargo..."
              className="w-full bg-[#070D1F] border border-white/15 rounded-xl pl-9 pr-4 py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70] transition-colors"
            />
            <Search className="w-4 h-4 text-[#CBAC70] absolute left-3 top-2.5" />
            {searchQuery && (
              <button
                onClick={() => setSearchQuery('')}
                className="absolute right-3 top-2 text-xs text-[#94A3B8] hover:text-white"
              >
                ✕
              </button>
            )}
          </div>

          {/* Sort Dropdown */}
          <div className="flex items-center gap-2 self-end sm:self-auto shrink-0">
            <span className="text-[11px] text-[#94A3B8] hidden sm:inline">Urutkan:</span>
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="bg-[#070D1F] border border-white/15 rounded-xl px-3 py-2 text-xs text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
            >
              <option value="featured">Paling Populer</option>
              <option value="sold">Terlaris (Sold)</option>
              <option value="price-low">Harga: Rendah ke Tinggi</option>
              <option value="price-high">Harga: Tinggi ke Rendah</option>
              <option value="rating">Rating Bintang</option>
            </select>
          </div>

        </div>

        {/* Quick Filter Chips (Size, New, Best) */}
        <div className="flex items-center justify-between flex-wrap gap-2 pt-2 border-t border-white/5 text-xs">
          
          {/* Size Pills */}
          <div className="flex items-center gap-1.5 overflow-x-auto scrollbar-none max-w-full">
            <span className="text-[11px] text-[#94A3B8] shrink-0 mr-1">Size:</span>
            {sizeOptions.map((s) => (
              <button
                key={s}
                onClick={() => setSelectedSize(s)}
                className={`px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition-all shrink-0 ${
                  selectedSize === s
                    ? 'bg-[#CBAC70] text-[#0B132B]'
                    : 'bg-[#070D1F] text-[#94A3B8] border border-white/10 hover:border-[#CBAC70]/40'
                }`}
              >
                {s === 'all' ? 'ALL' : s}
              </button>
            ))}
          </div>

          {/* Toggles */}
          <div className="flex items-center gap-2 shrink-0 ml-auto text-[11px] text-[#94A3B8]">
            <button
              onClick={() => setOnlyNewDrops(!onlyNewDrops)}
              className={`px-2.5 py-1 rounded-lg font-semibold border transition-all ${
                onlyNewDrops
                  ? 'bg-[#CBAC70]/20 border-[#CBAC70] text-[#CBAC70]'
                  : 'bg-[#070D1F] border-white/10 text-[#94A3B8]'
              }`}
            >
              New Drops Only
            </button>

            <button
              onClick={() => setOnlyBestSellers(!onlyBestSellers)}
              className={`px-2.5 py-1 rounded-lg font-semibold border transition-all ${
                onlyBestSellers
                  ? 'bg-[#CBAC70]/20 border-[#CBAC70] text-[#CBAC70]'
                  : 'bg-[#070D1F] border-white/10 text-[#94A3B8]'
              }`}
            >
              Bestsellers
            </button>

            {(selectedCategory !== 'all' || searchQuery || selectedSize !== 'all' || onlyNewDrops || onlyBestSellers) && (
              <button
                onClick={handleResetFilters}
                className="text-[#CBAC70] hover:underline flex items-center gap-1 font-bold pl-1"
                title="Reset"
              >
                <RotateCcw className="w-3 h-3" /> Reset
              </button>
            )}
          </div>

        </div>

      </div>

      {/* 4. Product Catalog Listing (2-Column Mobile First & 3-4 Col Desktop) */}
      <section className="space-y-3">
        
        <div className="flex items-center justify-between text-xs text-[#94A3B8] px-1">
          <span>Menampilkan <strong className="text-[#CBAC70] font-bold">{filteredProducts.length}</strong> produk artikel</span>
          <span className="text-[11px] text-[#CBAC70] font-mono">100% Original Malega Studio</span>
        </div>

        {filteredProducts.length === 0 ? (
          <div className="rounded-2xl bg-[#0E1736] border border-white/10 p-10 text-center space-y-3">
            <p className="text-sm font-bold text-[#FDFCFF]">Tidak ada produk yang sesuai dengan filter Anda</p>
            <p className="text-xs text-[#94A3B8]">Coba ubah kata kunci atau reset filter ukuran/kategori.</p>
            <button
              onClick={handleResetFilters}
              className="px-5 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase"
            >
              Reset Filter
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-5">
            {filteredProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}

      </section>

    </div>
  );
}
