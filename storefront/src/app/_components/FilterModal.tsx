'use client';

import React from 'react';
import { SlidersHorizontal, X, Check } from 'lucide-react';

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
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-in fade-in">
      <div className="bg-[#0E1736] border-t sm:border border-[#CBAC70]/40 rounded-t-3xl sm:rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-5 animate-in slide-in-from-bottom sm:zoom-in-95 text-[#FDFCFF] max-h-[85vh] overflow-y-auto">
        {/* Modal Header */}
        <div className="flex items-center justify-between border-b border-white/10 pb-3">
          <div className="flex items-center gap-2">
            <SlidersHorizontal className="w-4 h-4 text-[#CBAC70]" />
            <h3 className="font-bold text-sm text-[#FDFCFF] uppercase tracking-wider">
              Filter & Urutkan Produk
            </h3>
          </div>
          <button
            onClick={onClose}
            className="text-[#94A3B8] hover:text-white p-1 cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Sort Options in Modal */}
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
              { id: 'rating', label: 'Rating Bintang Tertinggi' },
            ].map((s) => (
              <button
                key={s.id}
                onClick={() => setSortBy(s.id)}
                className={`p-2.5 rounded-xl text-left font-semibold flex items-center justify-between border transition-all cursor-pointer ${
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

        {/* Size Options in Modal */}
        <div className="space-y-2 pt-2 border-t border-white/10">
          <label className="font-bold text-xs text-[#CBAC70] block uppercase tracking-wider">
            Pilih Ukuran (Size):
          </label>
          <div className="flex flex-wrap gap-1.5">
            {sizeOptions.map((s) => (
              <button
                key={s}
                onClick={() => setSelectedSize(s)}
                className={`min-w-[40px] py-1.5 px-3 rounded-xl text-xs font-bold border transition-all cursor-pointer ${
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

        {/* Special Drops Toggle */}
        <div className="space-y-2 pt-2 border-t border-white/10">
          <label className="font-bold text-xs text-[#CBAC70] block uppercase tracking-wider">
            Koleksi Khusus:
          </label>
          <div className="grid grid-cols-2 gap-2 text-xs">
            <button
              onClick={() => setOnlyNewDrops(!onlyNewDrops)}
              className={`p-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer ${
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
              className={`p-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer ${
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

        {/* Modal Bottom CTA */}
        <div className="pt-3 border-t border-white/10 flex items-center gap-2">
          <button
            onClick={handleResetFilters}
            className="w-1/3 py-3 rounded-xl bg-[#070D1F] border border-white/10 text-xs font-bold text-[#94A3B8] hover:text-white cursor-pointer"
          >
            Reset
          </button>

          <button
            onClick={onClose}
            className="w-2/3 py-3 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] text-xs font-black uppercase tracking-wider shadow-lg cursor-pointer"
          >
            Terapkan ({filteredProductsCount} Produk)
          </button>
        </div>
      </div>
    </div>
  );
}
