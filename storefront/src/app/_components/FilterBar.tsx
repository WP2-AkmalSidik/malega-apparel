'use client';

import React from 'react';
import { Search, SlidersHorizontal, X, ArrowUpDown } from 'lucide-react';
import { Product } from '../../types';

interface FilterBarProps {
  initialProducts: Product[];
  searchQuery: string;
  setSearchQuery: (q: string) => void;
  sortBy: string;
  setSortBy: (s: string) => void;
  activeFilterCount: number;
  setIsFilterModalOpen: (open: boolean) => void;
  categories: Array<{ id: string; label: string }>;
  selectedCategory: string;
  setSelectedCategory: (c: string) => void;
  selectedSize: string;
  setSelectedSize: (s: string) => void;
  onlyNewDrops: boolean;
  setOnlyNewDrops: (v: boolean) => void;
  onlyBestSellers: boolean;
  setOnlyBestSellers: (v: boolean) => void;
  handleResetFilters: () => void;
}

export default function FilterBar({
  initialProducts,
  searchQuery,
  setSearchQuery,
  sortBy,
  setSortBy,
  activeFilterCount,
  setIsFilterModalOpen,
  categories,
  selectedCategory,
  setSelectedCategory,
  selectedSize,
  setSelectedSize,
  onlyNewDrops,
  setOnlyNewDrops,
  onlyBestSellers,
  setOnlyBestSellers,
  handleResetFilters,
}: FilterBarProps) {
  return (
    <div className="space-y-3">
      {/* 1. Search & Filter Controls */}
      <div className="space-y-2">
        {/* Single Row: Search Bar + Filter/Sort Controls */}
        <div className="flex items-center gap-2">
          
          {/* Search Bar Input (Takes flex-1) */}
          <div className="relative flex-1">
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari kaos, hoodie, celana..."
              className="w-full bg-[#0E1736] border border-white/10 hover:border-white/20 focus:border-[#CBAC70] rounded-xl pl-9 pr-8 py-2.5 sm:py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none transition-colors shadow-sm"
              aria-label="Pencarian katalog produk"
            />
            <Search className="w-4 h-4 text-[#CBAC70] absolute left-3 top-3 sm:top-2.5 pointer-events-none" />
            {searchQuery && (
              <button
                type="button"
                onClick={() => setSearchQuery('')}
                className="absolute right-2.5 top-2.5 sm:top-2 p-1 text-[#94A3B8] hover:text-white cursor-pointer"
                title="Hapus pencarian"
                aria-label="Hapus kata kunci pencarian"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>

          {/* Mobile Filter & Urutkan Trigger (Sejajar dengan Pencarian) */}
          <button
            type="button"
            onClick={() => setIsFilterModalOpen(true)}
            className={`sm:hidden h-[41px] px-3.5 rounded-xl text-xs font-bold border transition-all duration-200 flex items-center justify-center gap-1.5 shrink-0 shadow-sm active:scale-95 cursor-pointer ${
              activeFilterCount > 0
                ? 'bg-[#14204A] border-[#CBAC70] text-[#CBAC70]'
                : 'bg-[#0E1736] border-white/10 text-[#FDFCFF]'
            }`}
            title="Filter & Urutkan Produk"
            aria-label="Filter dan urutkan produk"
          >
            <SlidersHorizontal className="w-3.5 h-3.5 text-[#CBAC70]" />
            <span>Filter</span>
            {activeFilterCount > 0 && (
              <span className="w-4 h-4 rounded-full bg-[#CBAC70] text-[#0B132B] font-black text-[9px] flex items-center justify-center ml-0.5">
                {activeFilterCount}
              </span>
            )}
          </button>

          {/* Desktop Sort Dropdown */}
          <div className="hidden sm:flex items-center gap-1.5 bg-[#0E1736] border border-white/10 hover:border-white/20 rounded-xl px-3 py-2 shrink-0 transition-colors">
            <ArrowUpDown className="w-3.5 h-3.5 text-[#CBAC70]" />
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="bg-transparent text-xs text-[#FDFCFF] focus:outline-none cursor-pointer pr-1"
              aria-label="Urutkan produk"
            >
              <option value="featured" className="bg-[#0B132B]">Paling Populer</option>
              <option value="sold" className="bg-[#0B132B]">Terlaris (Sold)</option>
              <option value="price-low" className="bg-[#0B132B]">Harga: Rendah ke Tinggi</option>
              <option value="price-high" className="bg-[#0B132B]">Harga: Tinggi ke Rendah</option>
              <option value="rating" className="bg-[#0B132B]">Rating Tertinggi</option>
            </select>
          </div>

          {/* Desktop Filter Modal Trigger Button */}
          <button
            type="button"
            onClick={() => setIsFilterModalOpen(true)}
            className={`hidden sm:flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold border transition-all duration-200 shrink-0 shadow-sm active:scale-95 cursor-pointer ${
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
      </div>

      {/* 2. Horizontal Category Carousel Tabs with Smooth Scroll */}
      <div className="relative overflow-hidden -mx-3 px-3 sm:mx-0 sm:px-0">
        <div className="overflow-x-auto scrollbar-none">
          <div className="flex items-center gap-1.5 min-w-max py-0.5 pr-4">
            {categories.map((cat) => {
              const count =
                cat.id === 'all'
                  ? initialProducts.length
                  : initialProducts.filter((p) => p.category === cat.id).length;
              const isSelected = selectedCategory === cat.id;

              return (
                <button
                  key={cat.id}
                  onClick={() => setSelectedCategory(cat.id)}
                  className={`px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 active:scale-95 cursor-pointer ${
                    isSelected
                      ? 'bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] shadow-sm'
                      : 'bg-[#0E1736] hover:bg-[#14204A] text-[#94A3B8] hover:text-[#FDFCFF] border border-white/5'
                  }`}
                >
                  <span>{cat.label}</span>
                  <span
                    className={`text-[9px] px-1.5 py-0.2 rounded-full font-mono ${
                      isSelected ? 'bg-[#0B132B] text-[#CBAC70]' : 'bg-[#070D1F] text-[#94A3B8]'
                    }`}
                  >
                    {count}
                  </span>
                </button>
              );
            })}
          </div>
        </div>
      </div>

      {/* 3. Active Applied Filter Tags */}
      {(selectedCategory !== 'all' ||
        searchQuery ||
        selectedSize !== 'all' ||
        onlyNewDrops ||
        onlyBestSellers ||
        sortBy !== 'featured') && (
        <div className="flex items-center gap-1.5 flex-wrap text-[10px] text-[#94A3B8] pt-0.5">
          <span className="font-semibold text-[#CBAC70]">Filter Aktif:</span>

          {selectedCategory !== 'all' && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>{selectedCategory}</span>
              <button
                type="button"
                onClick={() => setSelectedCategory('all')}
                className="hover:text-white cursor-pointer ml-0.5"
                title="Hapus filter kategori"
              >
                ✕
              </button>
            </span>
          )}

          {selectedSize !== 'all' && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Ukuran: {selectedSize}</span>
              <button
                type="button"
                onClick={() => setSelectedSize('all')}
                className="hover:text-white cursor-pointer ml-0.5"
                title="Hapus filter ukuran"
              >
                ✕
              </button>
            </span>
          )}

          {onlyNewDrops && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>New Drops</span>
              <button
                type="button"
                onClick={() => setOnlyNewDrops(false)}
                className="hover:text-white cursor-pointer ml-0.5"
                title="Hapus filter New Drops"
              >
                ✕
              </button>
            </span>
          )}

          {onlyBestSellers && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Terlaris</span>
              <button
                type="button"
                onClick={() => setOnlyBestSellers(false)}
                className="hover:text-white cursor-pointer ml-0.5"
                title="Hapus filter Terlaris"
              >
                ✕
              </button>
            </span>
          )}

          {sortBy !== 'featured' && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Urutan: {sortBy}</span>
              <button
                type="button"
                onClick={() => setSortBy('featured')}
                className="hover:text-white cursor-pointer ml-0.5"
                title="Reset urutan"
              >
                ✕
              </button>
            </span>
          )}

          <button
            type="button"
            onClick={handleResetFilters}
            className="text-[10px] text-[#CBAC70] hover:underline font-semibold ml-1 cursor-pointer"
          >
            Hapus semua filter
          </button>
        </div>
      )}
    </div>
  );
}
