'use client';

import React, { useState, useMemo, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import { 
  Search, 
  SlidersHorizontal, 
  Tag, 
  RotateCcw, 
  X,
  Check,
  ArrowUpDown
} from 'lucide-react';
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
  const [isFilterModalOpen, setIsFilterModalOpen] = useState<boolean>(false);

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
      if (selectedCategory !== 'all' && p.category !== selectedCategory) return false;
      if (searchQuery.trim() !== '') {
        const q = searchQuery.toLowerCase();
        const matchTitle = p.title.toLowerCase().includes(q);
        const matchSubtitle = p.subtitle.toLowerCase().includes(q);
        const matchDesc = p.description.toLowerCase().includes(q);
        const matchCat = p.category.toLowerCase().includes(q);
        const matchMaterial = p.material.toLowerCase().includes(q);
        if (!matchTitle && !matchSubtitle && !matchDesc && !matchCat && !matchMaterial) return false;
      }
      if (selectedSize !== 'all' && !p.sizes.includes(selectedSize)) return false;
      if (onlyNewDrops && !p.isNewDrop) return false;
      if (onlyBestSellers && !p.isBestSeller) return false;
      return true;
    }).sort((a, b) => {
      if (sortBy === 'price-low') return a.price - b.price;
      if (sortBy === 'price-high') return b.price - a.price;
      if (sortBy === 'sold') return b.soldCount - a.soldCount;
      if (sortBy === 'rating') return b.rating - a.rating;
      return 0;
    });
  }, [selectedCategory, searchQuery, selectedSize, onlyNewDrops, onlyBestSellers, sortBy]);

  const activeFilterCount = useMemo(() => {
    let count = 0;
    if (selectedSize !== 'all') count++;
    if (onlyNewDrops) count++;
    if (onlyBestSellers) count++;
    if (sortBy !== 'featured') count++;
    return count;
  }, [selectedSize, onlyNewDrops, onlyBestSellers, sortBy]);

  const handleResetFilters = () => {
    setSelectedCategory('all');
    setSearchQuery('');
    setSelectedSize('all');
    setSortBy('featured');
    setOnlyNewDrops(false);
    setOnlyBestSellers(false);
  };

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-5 space-y-3.5">
      
      {/* 1. Sleek Search & Filter Toolbar */}
      <div className="flex items-center gap-2">
        <div className="relative flex-1">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Cari artikel kaos 300GSM, hoodie, cargo..."
            className="w-full bg-[#0E1736] border border-white/10 hover:border-white/20 focus:border-[#CBAC70] rounded-xl pl-8 sm:pl-9 pr-7 py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none transition-colors shadow-sm"
          />
          <Search className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#CBAC70] absolute left-2.5 sm:left-3 top-2.5" />
          {searchQuery && (
            <button
              onClick={() => setSearchQuery('')}
              className="absolute right-2.5 top-2.5 text-xs text-[#94A3B8] hover:text-white"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>

        <div className="hidden md:flex items-center gap-1.5 bg-[#0E1736] border border-white/10 rounded-xl px-2.5 py-1.5 shrink-0">
          <ArrowUpDown className="w-3.5 h-3.5 text-[#CBAC70]" />
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="bg-transparent text-xs text-[#FDFCFF] focus:outline-none cursor-pointer pr-1"
          >
            <option value="featured" className="bg-[#0B132B]">Paling Populer</option>
            <option value="sold" className="bg-[#0B132B]">Terlaris (Sold)</option>
            <option value="price-low" className="bg-[#0B132B]">Harga: Rendah ke Tinggi</option>
            <option value="price-high" className="bg-[#0B132B]">Harga: Tinggi ke Rendah</option>
            <option value="rating" className="bg-[#0B132B]">Rating Tertinggi</option>
          </select>
        </div>

        <button
          onClick={() => setIsFilterModalOpen(true)}
          className={`flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold border transition-all duration-200 shrink-0 shadow-sm active:scale-95 ${
            activeFilterCount > 0
              ? 'bg-[#14204A] border-[#CBAC70] text-[#CBAC70]'
              : 'bg-[#0E1736] border-white/10 hover:border-[#CBAC70]/40 text-[#FDFCFF]'
          }`}
        >
          <SlidersHorizontal className="w-3.5 h-3.5 text-[#CBAC70]" />
          <span>Filter</span>
          {activeFilterCount > 0 && (
            <span className="w-4 h-4 rounded-full bg-[#CBAC70] text-[#0B132B] font-black text-[9px] flex items-center justify-center">
              {activeFilterCount}
            </span>
          )}
        </button>
      </div>

      {/* 2. Horizontal Category Switcher */}
      <div className="overflow-x-auto scrollbar-none -mx-3 px-3 sm:mx-0 sm:px-0">
        <div className="flex items-center gap-1.5 min-w-max pb-0.5">
          {categories.map((cat) => {
            const count = cat.id === 'all' 
              ? productsCatalog.length 
              : productsCatalog.filter(p => p.category === cat.id).length;
            const isSelected = selectedCategory === cat.id;

            return (
              <button
                key={cat.id}
                onClick={() => setSelectedCategory(cat.id)}
                className={`px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 active:scale-95 ${
                  isSelected
                    ? 'bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] shadow-sm'
                    : 'bg-[#0E1736] hover:bg-[#14204A] text-[#94A3B8] hover:text-[#FDFCFF] border border-white/5'
                }`}
              >
                <span>{cat.label}</span>
                <span className={`text-[9px] px-1.5 py-0.1 rounded-full font-mono ${isSelected ? 'bg-[#0B132B] text-[#CBAC70]' : 'bg-[#070D1F] text-[#94A3B8]'}`}>
                  {count}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      {/* 3. Applied Filters Strip */}
      {(selectedCategory !== 'all' || searchQuery || selectedSize !== 'all' || onlyNewDrops || onlyBestSellers || sortBy !== 'featured') && (
        <div className="flex items-center gap-1.5 flex-wrap text-[10px] text-[#94A3B8] pt-0.5">
          <span className="font-semibold text-[#CBAC70]">Filter:</span>

          {selectedCategory !== 'all' && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>{selectedCategory}</span>
              <button onClick={() => setSelectedCategory('all')} className="hover:text-white">✕</button>
            </span>
          )}

          {selectedSize !== 'all' && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Size: {selectedSize}</span>
              <button onClick={() => setSelectedSize('all')} className="hover:text-white">✕</button>
            </span>
          )}

          {onlyNewDrops && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>New Drops</span>
              <button onClick={() => setOnlyNewDrops(false)} className="hover:text-white">✕</button>
            </span>
          )}

          {onlyBestSellers && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Bestseller</span>
              <button onClick={() => setOnlyBestSellers(false)} className="hover:text-white">✕</button>
            </span>
          )}

          <button onClick={handleResetFilters} className="text-[10px] text-[#94A3B8] hover:text-[#CBAC70] underline ml-1">
            Reset Semua
          </button>
        </div>
      )}

      {/* 4. Product Catalog Grid */}
      <section className="space-y-2.5 pt-1">
        <div className="flex items-center justify-between text-[11px] text-[#94A3B8] px-0.5">
          <span>Menampilkan <strong className="text-[#CBAC70] font-bold">{filteredProducts.length}</strong> produk artikel</span>
          <span className="text-[#CBAC70] font-mono text-[10px]">100% Bespoke Studio</span>
        </div>

        {filteredProducts.length === 0 ? (
          <div className="rounded-2xl bg-[#0E1736] border border-white/10 p-8 text-center space-y-3">
            <p className="text-sm font-bold text-[#FDFCFF]">Tidak ada produk yang sesuai filter</p>
            <button
              onClick={handleResetFilters}
              className="px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase"
            >
              Reset Filter
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 lg:gap-5">
            {filteredProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}
      </section>

      {/* 5. Filter Modal & Bottom Sheet */}
      {isFilterModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-in fade-in">
          <div className="bg-[#0E1736] border-t sm:border border-[#CBAC70]/40 rounded-t-3xl sm:rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-5 animate-in slide-in-from-bottom sm:zoom-in-95 text-[#FDFCFF] max-h-[85vh] overflow-y-auto">
            
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <div className="flex items-center gap-2">
                <SlidersHorizontal className="w-4 h-4 text-[#CBAC70]" />
                <h3 className="font-bold text-sm text-[#FDFCFF] uppercase tracking-wider">
                  Filter & Urutkan Produk
                </h3>
              </div>
              <button onClick={() => setIsFilterModalOpen(false)} className="text-[#94A3B8] hover:text-white p-1">
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="space-y-2">
              <label className="font-bold text-xs text-[#CBAC70] block uppercase tracking-wider">
                Urutkan Berdasarkan:
              </label>
              <div className="grid grid-cols-1 gap-1.5 text-xs">
                {[
                  { id: 'featured', label: 'Paling Populer (Default)' },
                  { id: 'sold', label: 'Terlaris (Sold Count)' },
                  { id: 'price-low', label: 'Harga: Termurah ke Termahal' },
                  { id: 'price-high', label: 'Harga: Termahal ke Termurah' },
                  { id: 'rating', label: 'Rating Bintang Tertinggi' }
                ].map((s) => (
                  <button
                    key={s.id}
                    onClick={() => setSortBy(s.id)}
                    className={`p-2.5 rounded-xl text-left font-semibold flex items-center justify-between border transition-all ${
                      sortBy === s.id
                        ? 'bg-[#14204A] border-[#CBAC70] text-[#CBAC70]'
                        : 'bg-[#070D1F] border-white/5 text-[#94A3B8] hover:text-white'
                    }`}
                  >
                    <span>{s.label}</span>
                    {sortBy === s.id && <Check className="w-3.5 h-3.5 stroke-[3]" />}
                  </button>
                ))}
              </div>
            </div>

            <div className="space-y-2 pt-2 border-t border-white/10">
              <label className="font-bold text-xs text-[#CBAC70] block uppercase tracking-wider">
                Pilih Ukuran (Size):
              </label>
              <div className="flex flex-wrap gap-1.5">
                {sizeOptions.map((s) => (
                  <button
                    key={s}
                    onClick={() => setSelectedSize(s)}
                    className={`min-w-[40px] py-1.5 px-3 rounded-xl text-xs font-bold border transition-all ${
                      selectedSize === s
                        ? 'bg-[#CBAC70] text-[#0B132B] border-[#CBAC70] shadow'
                        : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:text-white'
                    }`}
                  >
                    {s === 'all' ? 'SEMUA' : s}
                  </button>
                ))}
              </div>
            </div>

            <div className="space-y-2 pt-2 border-t border-white/10">
              <label className="font-bold text-xs text-[#CBAC70] block uppercase tracking-wider">
                Koleksi Khusus:
              </label>
              <div className="grid grid-cols-2 gap-2 text-xs">
                <button
                  onClick={() => setOnlyNewDrops(!onlyNewDrops)}
                  className={`p-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all ${
                    onlyNewDrops
                      ? 'bg-[#14204A] border-[#CBAC70] text-[#CBAC70]'
                      : 'bg-[#070D1F] border-white/5 text-[#94A3B8]'
                  }`}
                >
                  {onlyNewDrops && <Check className="w-3 h-3" />}
                  <span>New Drops SS26</span>
                </button>

                <button
                  onClick={() => setOnlyBestSellers(!onlyBestSellers)}
                  className={`p-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all ${
                    onlyBestSellers
                      ? 'bg-[#14204A] border-[#CBAC70] text-[#CBAC70]'
                      : 'bg-[#070D1F] border-white/5 text-[#94A3B8]'
                  }`}
                >
                  {onlyBestSellers && <Check className="w-3 h-3" />}
                  <span>Bestsellers</span>
                </button>
              </div>
            </div>

            <div className="pt-3 border-t border-white/10 flex items-center gap-2">
              <button
                onClick={handleResetFilters}
                className="w-1/3 py-3 rounded-xl bg-[#070D1F] border border-white/10 text-xs font-bold text-[#94A3B8] hover:text-white"
              >
                Reset
              </button>

              <button
                onClick={() => setIsFilterModalOpen(false)}
                className="w-2/3 py-3 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] text-xs font-black uppercase tracking-wider shadow-lg"
              >
                Terapkan ({filteredProducts.length} Produk)
              </button>
            </div>

          </div>
        </div>
      )}

    </div>
  );
}

export default function CatalogPage() {
  return (
    <Suspense fallback={<div className="p-8 text-center text-[#CBAC70]">Memuat Katalog Malega...</div>}>
      <CatalogContent />
    </Suspense>
  );
}
