'use client';

import React, { useEffect, useMemo } from 'react';
import { SlidersHorizontal, X, Check, Sparkles, Flame, RotateCcw } from 'lucide-react';

interface FilterModalProps {
  isOpen: boolean;
  onClose: () => void;
  sortBy: string;
  setSortBy: (s: string) => void;
  sizeOptions: string[];
  selectedSize: string;
  setSelectedSize: (s: string) => void;
  onlyNewDrops: boolean;
  setOnlyNewDrops: (v: boolean) => void;
  onlyBestSellers: boolean;
  setOnlyBestSellers: (v: boolean) => void;
  handleResetFilters: () => void;
  filteredProductsCount: number;
}

export default function FilterModal({
  isOpen,
  onClose,
  sortBy,
  setSortBy,
  sizeOptions,
  selectedSize,
  setSelectedSize,
  onlyNewDrops,
  setOnlyNewDrops,
  onlyBestSellers,
  setOnlyBestSellers,
  handleResetFilters,
  filteredProductsCount,
}: FilterModalProps) {
  // Lock body scroll and listen for Escape key
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
    };
    if (isOpen) {
      window.addEventListener('keydown', handleKeyDown);
      document.body.style.overflow = 'hidden';
    }
    return () => {
      window.removeEventListener('keydown', handleKeyDown);
      document.body.style.overflow = 'unset';
    };
  }, [isOpen, onClose]);

  // Categorize and sort sizes logically for apparel, pants, and accessories
  const categorizedSizes = useMemo(() => {
    const letterOrder = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'];
    const apparel: string[] = [];
    const pants: string[] = [];
    const accessories: string[] = [];

    sizeOptions.forEach((s) => {
      if (s === 'all') return;
      const num = parseInt(s);
      if (!isNaN(num) && String(num) === s.trim()) {
        pants.push(s);
      } else if (letterOrder.includes(s.toUpperCase())) {
        apparel.push(s);
      } else {
        accessories.push(s);
      }
    });

    apparel.sort((a, b) => letterOrder.indexOf(a.toUpperCase()) - letterOrder.indexOf(b.toUpperCase()));
    pants.sort((a, b) => parseInt(a) - parseInt(b));
    accessories.sort((a, b) => a.localeCompare(b));

    return { apparel, pants, accessories };
  }, [sizeOptions]);

  if (!isOpen) return null;

  const sortOptionsList = [
    { id: 'featured', label: 'Paling Populer', desc: 'Rekomendasi kurasi' },
    { id: 'sold', label: 'Terlaris', desc: 'Berdasarkan jumlah terjual' },
    { id: 'price-low', label: 'Harga: Termurah', desc: 'Harga terendah ke tertinggi' },
    { id: 'price-high', label: 'Harga: Termahal', desc: 'Koleksi eksklusif teratas' },
    { id: 'rating', label: 'Rating Tertinggi', desc: 'Ulasan bintang tertinggi' },
  ];

  return (
    <div 
      className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-in fade-in duration-200"
      onClick={onClose}
      role="dialog"
      aria-modal="true"
      aria-labelledby="filter-modal-title"
    >
      <div 
        className="bg-[#0B132B] border-t sm:border border-[#CBAC70]/30 rounded-t-3xl sm:rounded-2xl max-w-lg w-full shadow-2xl animate-in slide-in-from-bottom sm:zoom-in-95 duration-200 text-[#FDFCFF] max-h-[90vh] sm:max-h-[85vh] flex flex-col overflow-hidden"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Mobile Grab Handle Bar */}
        <div className="pt-3 pb-1 flex justify-center sm:hidden shrink-0">
          <div className="w-12 h-1 bg-white/20 rounded-full" />
        </div>

        {/* FIXED HEADER */}
        <div className="px-5 py-4 border-b border-white/10 flex items-center justify-between shrink-0 bg-[#0E1736]">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-xl bg-[#14204A] border border-[#CBAC70]/40 flex items-center justify-center text-[#CBAC70]">
              <SlidersHorizontal className="w-4 h-4" />
            </div>
            <div>
              <h3 id="filter-modal-title" className="font-bold text-sm sm:text-base text-[#FDFCFF]">
                Filter & Urutkan
              </h3>
              <p className="text-[11px] text-[#94A3B8]">
                Sesuaikan tampilan produk sesuai preferensi
              </p>
            </div>
          </div>
          <button
            type="button"
            onClick={onClose}
            className="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 text-[#94A3B8] hover:text-white flex items-center justify-center transition-colors cursor-pointer"
            aria-label="Tutup modal filter"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* SCROLLABLE BODY */}
        <div className="flex-1 overflow-y-auto px-5 py-5 space-y-6">
          
          {/* 1. Sort Options */}
          <div className="space-y-2.5">
            <div className="flex items-center justify-between">
              <label className="text-xs font-bold text-[#CBAC70] uppercase tracking-wider">
                Urutkan Berdasarkan
              </label>
              <span className="text-[11px] text-[#94A3B8]">Pilih salah satu</span>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
              {sortOptionsList.map((s) => {
                const isSelected = sortBy === s.id;
                return (
                  <button
                    key={s.id}
                    type="button"
                    onClick={() => setSortBy(s.id)}
                    className={`p-3 rounded-xl text-left border transition-all cursor-pointer flex items-center justify-between gap-2 ${
                      isSelected
                        ? 'bg-[#14204A] border-[#CBAC70] text-[#FDFCFF] shadow-sm'
                        : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:border-white/20 hover:text-white'
                    }`}
                  >
                    <div className="min-w-0">
                      <div className={`text-xs font-bold truncate ${isSelected ? 'text-[#CBAC70]' : 'text-[#FDFCFF]'}`}>
                        {s.label}
                      </div>
                      <div className="text-[10px] text-[#94A3B8] truncate mt-0.5">
                        {s.desc}
                      </div>
                    </div>
                    <div className={`w-5 h-5 rounded-full border shrink-0 flex items-center justify-center ${
                      isSelected ? 'bg-[#CBAC70] border-[#CBAC70] text-[#0B132B]' : 'border-white/20'
                    }`}>
                      {isSelected && <Check className="w-3 h-3 stroke-[3]" />}
                    </div>
                  </button>
                );
              })}
            </div>
          </div>

          {/* 2. Size Options */}
          <div className="space-y-3 pt-4 border-t border-white/10">
            <div className="flex items-center justify-between">
              <label className="text-xs font-bold text-[#CBAC70] uppercase tracking-wider">
                Pilih Ukuran (Size)
              </label>
              {selectedSize !== 'all' && (
                <button
                  type="button"
                  onClick={() => setSelectedSize('all')}
                  className="text-[11px] text-[#CBAC70] hover:underline cursor-pointer"
                >
                  Reset Ukuran
                </button>
              )}
            </div>

            {/* All Sizes Button */}
            <div className="flex flex-wrap gap-2">
              <button
                type="button"
                onClick={() => setSelectedSize('all')}
                className={`py-2 px-4 rounded-xl text-xs font-bold border transition-all cursor-pointer ${
                  selectedSize === 'all'
                    ? 'bg-[#CBAC70] text-[#0B132B] border-[#CBAC70] shadow'
                    : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:border-white/20 hover:text-white'
                }`}
              >
                Semua Ukuran
              </button>
            </div>

            {/* Group: Baju & Atasan (Letter Sizes) */}
            {categorizedSizes.apparel.length > 0 && (
              <div className="space-y-1.5 pt-1">
                <span className="text-[11px] font-medium text-[#94A3B8] block">
                  Kaos, Hoodie & Atasan:
                </span>
                <div className="flex flex-wrap gap-2">
                  {categorizedSizes.apparel.map((s) => {
                    const isSelected = selectedSize === s;
                    return (
                      <button
                        key={s}
                        type="button"
                        onClick={() => setSelectedSize(s)}
                        className={`min-w-[44px] h-10 px-3.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center justify-center ${
                          isSelected
                            ? 'bg-[#CBAC70] text-[#0B132B] border-[#CBAC70] shadow'
                            : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:border-white/20 hover:text-white'
                        }`}
                      >
                        {s}
                      </button>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Group: Celana & Denim (Number Sizes) */}
            {categorizedSizes.pants.length > 0 && (
              <div className="space-y-1.5 pt-1">
                <span className="text-[11px] font-medium text-[#94A3B8] block">
                  Celana & Denim (Waist):
                </span>
                <div className="flex flex-wrap gap-2">
                  {categorizedSizes.pants.map((s) => {
                    const isSelected = selectedSize === s;
                    return (
                      <button
                        key={s}
                        type="button"
                        onClick={() => setSelectedSize(s)}
                        className={`min-w-[44px] h-10 px-3.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center justify-center ${
                          isSelected
                            ? 'bg-[#CBAC70] text-[#0B132B] border-[#CBAC70] shadow'
                            : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:border-white/20 hover:text-white'
                        }`}
                      >
                        {s}
                      </button>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Group: Aksesoris & Lainnya */}
            {categorizedSizes.accessories.length > 0 && (
              <div className="space-y-1.5 pt-1">
                <span className="text-[11px] font-medium text-[#94A3B8] block">
                  Topi & Aksesoris:
                </span>
                <div className="flex flex-wrap gap-2">
                  {categorizedSizes.accessories.map((s) => {
                    const isSelected = selectedSize === s;
                    return (
                      <button
                        key={s}
                        type="button"
                        onClick={() => setSelectedSize(s)}
                        className={`h-10 px-3.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center justify-center ${
                          isSelected
                            ? 'bg-[#CBAC70] text-[#0B132B] border-[#CBAC70] shadow'
                            : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:border-white/20 hover:text-white'
                        }`}
                      >
                        {s}
                      </button>
                    );
                  })}
                </div>
              </div>
            )}
          </div>

          {/* 3. Special Collections Toggle */}
          <div className="space-y-2.5 pt-4 border-t border-white/10">
            <label className="text-xs font-bold text-[#CBAC70] uppercase tracking-wider block">
              Koleksi & Kategori Khusus
            </label>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <button
                type="button"
                onClick={() => setOnlyNewDrops(!onlyNewDrops)}
                className={`p-3 rounded-xl border font-semibold flex items-center justify-between transition-all cursor-pointer ${
                  onlyNewDrops
                    ? 'bg-[#14204A] border-[#CBAC70] text-[#FDFCFF]'
                    : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:border-white/20 hover:text-white'
                }`}
              >
                <div className="flex items-center gap-2.5">
                  <div className={`w-7 h-7 rounded-lg flex items-center justify-center ${onlyNewDrops ? 'bg-[#CBAC70]/20 text-[#CBAC70]' : 'bg-white/5 text-[#94A3B8]'}`}>
                    <Sparkles className="w-3.5 h-3.5" />
                  </div>
                  <span className="text-xs font-bold">Rilis Terbaru (New Drops)</span>
                </div>
                <div className={`w-5 h-5 rounded-md border shrink-0 flex items-center justify-center ${
                  onlyNewDrops ? 'bg-[#CBAC70] border-[#CBAC70] text-[#0B132B]' : 'border-white/20'
                }`}>
                  {onlyNewDrops && <Check className="w-3.5 h-3.5 stroke-[3]" />}
                </div>
              </button>

              <button
                type="button"
                onClick={() => setOnlyBestSellers(!onlyBestSellers)}
                className={`p-3 rounded-xl border font-semibold flex items-center justify-between transition-all cursor-pointer ${
                  onlyBestSellers
                    ? 'bg-[#14204A] border-[#CBAC70] text-[#FDFCFF]'
                    : 'bg-[#070D1F] border-white/10 text-[#94A3B8] hover:border-white/20 hover:text-white'
                }`}
              >
                <div className="flex items-center gap-2.5">
                  <div className={`w-7 h-7 rounded-lg flex items-center justify-center ${onlyBestSellers ? 'bg-[#CBAC70]/20 text-[#CBAC70]' : 'bg-white/5 text-[#94A3B8]'}`}>
                    <Flame className="w-3.5 h-3.5" />
                  </div>
                  <span className="text-xs font-bold">Produk Terlaris (Bestseller)</span>
                </div>
                <div className={`w-5 h-5 rounded-md border shrink-0 flex items-center justify-center ${
                  onlyBestSellers ? 'bg-[#CBAC70] border-[#CBAC70] text-[#0B132B]' : 'border-white/20'
                }`}>
                  {onlyBestSellers && <Check className="w-3.5 h-3.5 stroke-[3]" />}
                </div>
              </button>
            </div>
          </div>

        </div>

        {/* FIXED FOOTER */}
        <div className="p-4 sm:p-5 border-t border-white/10 bg-[#0E1736] flex items-center gap-3 shrink-0">
          <button
            type="button"
            onClick={handleResetFilters}
            className="w-1/3 py-3 px-3 rounded-xl bg-[#070D1F] hover:bg-white/5 border border-white/10 text-xs sm:text-sm font-semibold text-[#94A3B8] hover:text-white transition-all cursor-pointer flex items-center justify-center gap-1.5"
          >
            <RotateCcw className="w-3.5 h-3.5" />
            <span>Reset</span>
          </button>

          <button
            type="button"
            onClick={onClose}
            className="w-2/3 py-3 px-4 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:brightness-105 active:scale-[0.98] text-[#0B132B] text-xs sm:text-sm font-black uppercase tracking-wider shadow-lg transition-all cursor-pointer flex items-center justify-center gap-2"
          >
            <span>Terapkan</span>
            <span className="bg-[#0B132B]/20 px-2 py-0.5 rounded-full text-[11px] font-bold">
              {filteredProductsCount} Produk
            </span>
          </button>
        </div>
      </div>
    </div>
  );
}
