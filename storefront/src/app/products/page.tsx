'use client';

import React, { useState, useMemo, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import { Filter, SlidersHorizontal, ArrowUpDown, Search, RotateCcw, Sparkles } from 'lucide-react';
import { productsCatalog } from '../../data/products';
import ProductCard from '../../components/ProductCard';

function CatalogContent() {
  const searchParams = useSearchParams();
  const initialCategory = searchParams.get('category') || 'all';
  const initialSearch = searchParams.get('search') || '';

  const [selectedCategory, setSelectedCategory] = useState<string>(initialCategory);
  const [searchQuery, setSearchQuery] = useState<string>(initialSearch);
  const [selectedSize, setSelectedSize] = useState<string>('all');
  const [sortBy, setSortBy] = useState<string>('featured');
  const [onlyNewDrops, setOnlyNewDrops] = useState<boolean>(false);
  const [onlyBestSellers, setOnlyBestSellers] = useState<boolean>(false);
  const [mobileFilterOpen, setMobileFilterOpen] = useState<boolean>(false);

  const categories = ['all', 'T-Shirts', 'Outerwear', 'Bottoms', 'Accessories'];
  const allSizes = ['all', 'S', 'M', 'L', 'XL', 'XXL', '28', '30', '32', '34', '36'];

  const filteredProducts = useMemo(() => {
    return productsCatalog.filter(p => {
      // Category filter
      if (selectedCategory !== 'all' && p.category !== selectedCategory) {
        return false;
      }
      // Search query filter
      if (searchQuery.trim() !== '') {
        const q = searchQuery.toLowerCase();
        const matchTitle = p.title.toLowerCase().includes(q);
        const matchDesc = p.description.toLowerCase().includes(q);
        const matchCat = p.category.toLowerCase().includes(q);
        const matchMaterial = p.material.toLowerCase().includes(q);
        if (!matchTitle && !matchDesc && !matchCat && !matchMaterial) return false;
      }
      // Size filter
      if (selectedSize !== 'all' && !p.sizes.includes(selectedSize)) {
        return false;
      }
      // New drops filter
      if (onlyNewDrops && !p.isNewDrop) {
        return false;
      }
      // Bestsellers filter
      if (onlyBestSellers && !p.isBestSeller) {
        return false;
      }
      return true;
    }).sort((a, b) => {
      if (sortBy === 'price-low') return a.price - b.price;
      if (sortBy === 'price-high') return b.price - a.price;
      if (sortBy === 'rating') return b.rating - a.rating;
      if (sortBy === 'sold') return b.soldCount - a.soldCount;
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
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      
      {/* Header Banner */}
      <div className="bg-gradient-to-r from-[#111D42] via-[#0B132B] to-[#1C2541] border border-[#CBAC70]/30 rounded-3xl p-8 sm:p-10 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div className="space-y-2">
          <div className="flex items-center gap-2">
            <Sparkles className="w-4 h-4 text-[#CBAC70]" />
            <span className="text-xs font-bold text-[#CBAC70] uppercase tracking-widest font-mono">Bespoke Collection</span>
          </div>
          <h1 className="text-3xl sm:text-4xl font-black text-[#FDFCFF] uppercase tracking-wide">
            Product Catalog & Atelier
          </h1>
          <p className="text-xs sm:text-sm text-[#94A3B8] max-w-xl">
            Jelajahi seluruh rilisan apparel original Malega dengan material Combed Heavyweight 300GSM dan siluet kontemporer.
          </p>
        </div>

        {/* Quick Search */}
        <div className="w-full md:w-80">
          <div className="relative">
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari nama artikel, bahan, dll..."
              className="w-full bg-[#080E20] border border-[#CBAC70]/30 rounded-xl pl-10 pr-4 py-2.5 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
            />
            <Search className="w-4 h-4 text-[#CBAC70] absolute left-3.5 top-3" />
          </div>
        </div>
      </div>

      {/* Main Content Layout with Sidebar & Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {/* Desktop Filter Sidebar */}
        <aside className="hidden lg:block lg:col-span-3 space-y-6">
          <div className="luxury-card rounded-2xl p-6 space-y-6 sticky top-28">
            
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] flex items-center gap-2">
                <SlidersHorizontal className="w-4 h-4" /> Filter Catalog
              </h3>
              <button
                onClick={handleResetFilters}
                className="text-[11px] text-[#94A3B8] hover:text-[#CBAC70] flex items-center gap-1"
                title="Reset Filters"
              >
                <RotateCcw className="w-3 h-3" /> Reset
              </button>
            </div>

            {/* Category Filter */}
            <div className="space-y-2">
              <span className="text-xs font-bold text-[#FDFCFF] block">Categories</span>
              <div className="space-y-1">
                {categories.map((cat) => (
                  <button
                    key={cat}
                    onClick={() => setSelectedCategory(cat)}
                    className={`w-full text-left px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors ${
                      selectedCategory === cat
                        ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                        : 'text-[#94A3B8] hover:text-white hover:bg-white/5'
                    }`}
                  >
                    <span>{cat === 'all' ? 'All Collections' : cat}</span>
                    <span className="text-[10px] opacity-70">
                      {cat === 'all'
                        ? productsCatalog.length
                        : productsCatalog.filter(p => p.category === cat).length}
                    </span>
                  </button>
                ))}
              </div>
            </div>

            {/* Size Filter */}
            <div className="space-y-2 pt-4 border-t border-white/10">
              <span className="text-xs font-bold text-[#FDFCFF] block">Sizes</span>
              <div className="flex flex-wrap gap-1.5">
                {allSizes.map((size) => (
                  <button
                    key={size}
                    onClick={() => setSelectedSize(size)}
                    className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${
                      selectedSize === size
                        ? 'bg-[#CBAC70] text-[#0B132B] shadow'
                        : 'bg-[#080E20] border border-white/10 text-[#94A3B8] hover:text-white hover:border-[#CBAC70]/40'
                    }`}
                  >
                    {size === 'all' ? 'ALL' : size}
                  </button>
                ))}
              </div>
            </div>

            {/* Toggles */}
            <div className="space-y-3 pt-4 border-t border-white/10 text-xs text-[#94A3B8]">
              <label className="flex items-center gap-2 cursor-pointer hover:text-white">
                <input
                  type="checkbox"
                  checked={onlyNewDrops}
                  onChange={(e) => setOnlyNewDrops(e.target.checked)}
                  className="w-4 h-4 rounded border-white/20 accent-[#CBAC70]"
                />
                <span>Hanya New Drops SS26</span>
              </label>

              <label className="flex items-center gap-2 cursor-pointer hover:text-white">
                <input
                  type="checkbox"
                  checked={onlyBestSellers}
                  onChange={(e) => setOnlyBestSellers(e.target.checked)}
                  className="w-4 h-4 rounded border-white/20 accent-[#CBAC70]"
                />
                <span>Hanya Best Sellers</span>
              </label>
            </div>

          </div>
        </aside>

        {/* Product Grid & Controls */}
        <div className="lg:col-span-9 space-y-6">
          
          {/* Top Bar Controls */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-[#111D42]/60 border border-[#CBAC70]/20 text-xs">
            <div className="text-[#94A3B8]">
              Menampilkan <strong className="text-[#CBAC70]">{filteredProducts.length}</strong> produk artikel
            </div>

            <div className="flex items-center gap-3">
              <span className="text-[#94A3B8] hidden sm:inline">Urutkan:</span>
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="bg-[#080E20] border border-[#CBAC70]/30 rounded-xl px-3 py-1.5 text-xs text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
              >
                <option value="featured">Paling Populer</option>
                <option value="sold">Terlaris (Sold Count)</option>
                <option value="price-low">Harga: Rendah ke Tinggi</option>
                <option value="price-high">Harga: Tinggi ke Rendah</option>
                <option value="rating">Rating Tertinggi</option>
              </select>
            </div>
          </div>

          {/* Product Grid */}
          {filteredProducts.length === 0 ? (
            <div className="luxury-card rounded-3xl p-12 text-center space-y-4">
              <p className="text-base font-bold text-[#FDFCFF]">Tidak ada produk yang cocok dengan filter</p>
              <p className="text-xs text-[#94A3B8]">Coba sesuaikan kata kunci pencarian atau reset filter kategori.</p>
              <button
                onClick={handleResetFilters}
                className="px-5 py-2.5 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase"
              >
                Reset Semua Filter
              </button>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredProducts.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}

        </div>

      </div>

    </div>
  );
}

export default function CatalogPage() {
  return (
    <Suspense fallback={<div className="p-12 text-center text-[#CBAC70]">Memuat Katalog Malega...</div>}>
      <CatalogContent />
    </Suspense>
  );
}
