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
    <>
      {/* 1. Sleek Compact Search & Filter Toolbar (1 Single Flexible Row) */}
      <div className="flex items-center gap-2">
        {/* Search Bar Input */}
        <div className="relative flex-1">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Cari artikel kaos 300GSM, hoodie, cargo, kemeja..."
            className="w-full bg-[#0E1736] border border-white/10 hover:border-white/20 focus:border-[#CBAC70] rounded-xl pl-8 sm:pl-9 pr-7 py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none transition-colors shadow-sm"
          />
          <Search className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#CBAC70] absolute left-2.5 sm:left-3 top-2.5" />
          {searchQuery && (
            <button
              onClick={() => setSearchQuery('')}
              className="absolute right-2.5 top-2.5 text-xs text-[#94A3B8] hover:text-white cursor-pointer"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>

        {/* Sort Select */}
        <div className="hidden md:flex items-center gap-1.5 bg-[#0E1736] border border-white/10 rounded-xl px-2.5 py-1.5 shrink-0">
          <ArrowUpDown className="w-3.5 h-3.5 text-[#CBAC70]" />
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="bg-transparent text-xs text-[#FDFCFF] focus:outline-none cursor-pointer pr-1"
          >
            <option value="featured" className="bg-[#0B132B]">
              Paling Populer
            </option>
            <option value="sold" className="bg-[#0B132B]">
              Terlaris (Sold)
            </option>
            <option value="price-low" className="bg-[#0B132B]">
              Harga: Rendah ke Tinggi
            </option>
            <option value="price-high" className="bg-[#0B132B]">
              Harga: Tinggi ke Rendah
            </option>
            <option value="rating" className="bg-[#0B132B]">
              Rating Tertinggi
            </option>
          </select>
        </div>

        {/* Filter Trigger Button */}
        <button
          onClick={() => setIsFilterModalOpen(true)}
          className={`flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold border transition-all duration-200 shrink-0 shadow-sm active:scale-95 cursor-pointer ${
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

      {/* 2. Horizontal Category Carousel Tabs */}
      <div className="overflow-x-auto scrollbar-none -mx-3 px-3 sm:mx-0 sm:px-0">
        <div className="flex items-center gap-1.5 min-w-max pb-0.5">
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
                  className={`text-[9px] px-1.5 py-0.1 rounded-full font-mono ${
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
                onClick={() => setSelectedCategory('all')}
                className="hover:text-white cursor-pointer"
              >
                ✕
              </button>
            </span>
          )}

          {selectedSize !== 'all' && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Size: {selectedSize}</span>
              <button
                onClick={() => setSelectedSize('all')}
                className="hover:text-white cursor-pointer"
              >
                ✕
              </button>
            </span>
          )}

          {onlyNewDrops && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>New Drops</span>
              <button
                onClick={() => setOnlyNewDrops(false)}
                className="hover:text-white cursor-pointer"
              >
                ✕
              </button>
            </span>
          )}

          {onlyBestSellers && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Bestseller</span>
              <button
                onClick={() => setOnlyBestSellers(false)}
                className="hover:text-white cursor-pointer"
              >
                ✕
              </button>
            </span>
          )}

          {sortBy !== 'featured' && (
            <span className="inline-flex items-center gap-1 bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] px-2 py-0.5 rounded-lg">
              <span>Sort: {sortBy}</span>
              <button
                onClick={() => setSortBy('featured')}
                className="hover:text-white cursor-pointer"
              >
                ✕
              </button>
            </span>
          )}

          <button
            onClick={handleResetFilters}
            className="text-[10px] text-[#94A3B8] hover:text-[#CBAC70] underline ml-1 cursor-pointer"
          >
            Reset Semua
          </button>
        </div>
      )}
    </>
  );
}
