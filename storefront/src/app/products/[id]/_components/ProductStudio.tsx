'use client';

import React from 'react';
import { Star, Ruler, Heart, ShoppingBag, Zap } from 'lucide-react';
import { formatRupiah } from '../../../../lib/utils';
import { ColorOption, Product } from '../../../../types';

interface ProductStudioProps {
  product: Product;
  selectedColor: ColorOption;
  handleColorChange: (color: ColorOption) => void;
  selectedSize: string;
  handleSizeChange: (size: string) => void;
  quantity: number;
  handleQuantity: (delta: number) => void;
  activeVariant: any;
  currentPrice: number;
  currentCompareAt: number | null;
  setShowSizeChart: (show: boolean) => void;
  isCurrentProductFavorited: boolean;
  toggleWishlist: (id: string) => void;
  handleAddToBag: (e?: React.MouseEvent) => void;
  handleInstantBuy: (e?: React.MouseEvent) => void;
}

export default function ProductStudio({
  product,
  selectedColor,
  handleColorChange,
  selectedSize,
  handleSizeChange,
  quantity,
  handleQuantity,
  activeVariant,
  currentPrice,
  currentCompareAt,
  setShowSizeChart,
  isCurrentProductFavorited,
  toggleWishlist,
  handleAddToBag,
  handleInstantBuy,
}: ProductStudioProps) {
  const stockAvailable = activeVariant.availableStock || product.stockTotal || 10;

  return (
    <div className="lg:col-span-6 space-y-5">
      <div className="rounded-2xl sm:rounded-3xl bg-[#0E1736] border border-[#CBAC70]/30 shadow-2xl p-5 sm:p-7 space-y-6">
        {/* Title & Category Header */}
        <div className="space-y-2 border-b border-white/10 pb-4">
          <div className="flex items-center gap-2 text-[11px]">
            <span className="font-mono text-[#CBAC70] font-bold uppercase tracking-widest">
              {product.category} • {product.gsm ? `${product.gsm}GSM` : 'Bespoke'}
            </span>
            <span className="text-[#94A3B8]">|</span>
            <div className="flex items-center gap-1 text-[#CBAC70]">
              <Star className="w-3.5 h-3.5 fill-current" />
              <span className="font-bold">{product.rating}</span>
              <span className="text-[#94A3B8]">({product.reviewCount} ulasan)</span>
            </div>
          </div>

          <h1 className="text-xl sm:text-2xl font-black text-[#FDFCFF] leading-tight uppercase">
            {product.title}
          </h1>

          <p className="text-xs text-[#94A3B8] leading-relaxed">{product.subtitle}</p>
        </div>

        {/* Single Unified Synchronized Price Box */}
        <div className="p-3.5 sm:p-4 rounded-xl bg-[#0B132B] border border-[#CBAC70]/30 flex items-baseline justify-between shadow-inner">
          <div className="space-y-0.5">
            <div className="flex items-baseline gap-2.5">
              <span className="text-2xl sm:text-3xl font-black text-[#CBAC70] gold-gradient-pure">
                {formatRupiah(currentPrice * quantity)}
              </span>
              {currentCompareAt && currentCompareAt > currentPrice && (
                <span className="text-xs text-[#94A3B8] line-through">
                  {formatRupiah(currentCompareAt * quantity)}
                </span>
              )}
            </div>
            {quantity > 1 && (
              <p className="text-[10px] font-mono text-[#94A3B8]">
                ({formatRupiah(currentPrice)} × {quantity} pcs)
              </p>
            )}
          </div>

          <span className="text-[10px] sm:text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full">
            ✓ Ready Stock ({stockAvailable} pcs)
          </span>
        </div>

        {/* 1. Colorway Selection Swatches */}
        <div className="space-y-2">
          <div className="flex justify-between items-center text-xs">
            <span className="font-bold text-[#FDFCFF]">Pilihan Warna:</span>
            <span className="text-[#CBAC70] font-semibold font-mono">
              {selectedColor.name}
            </span>
          </div>

          <div className="flex flex-wrap gap-2">
            {product.colors.map((c, idx) => (
              <button
                type="button"
                key={idx}
                onClick={() => handleColorChange(c)}
                className={`px-3.5 py-2 rounded-xl border text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer ${
                  selectedColor.name === c.name
                    ? 'border-[#CBAC70] bg-[#14204A] text-[#FDFCFF] ring-2 ring-[#CBAC70] shadow-md scale-102 font-bold'
                    : 'border-white/15 hover:border-white/40 text-[#94A3B8] bg-[#0B132B]'
                }`}
              >
                <span
                  className="w-3.5 h-3.5 rounded-full border border-white/20 shrink-0"
                  style={{ backgroundColor: c.hex }}
                />
                <span>{c.name}</span>
              </button>
            ))}
          </div>
        </div>

        {/* 2. Size Selection */}
        <div className="space-y-2">
          <div className="flex justify-between items-center text-xs">
            <span className="font-bold text-[#FDFCFF]">Pilih Ukuran:</span>
            <button
              type="button"
              onClick={() => setShowSizeChart(true)}
              className="text-[#CBAC70] hover:underline flex items-center gap-1 font-semibold text-[11px] cursor-pointer"
            >
              <Ruler className="w-3.5 h-3.5" /> Panduan Ukuran
            </button>
          </div>

          <div className="flex flex-wrap gap-2">
            {product.sizes.map((s) => (
              <button
                type="button"
                key={s}
                onClick={() => handleSizeChange(s)}
                className={`min-w-[48px] py-2 px-3.5 rounded-xl border text-xs font-bold transition-all cursor-pointer flex items-center justify-center ${
                  selectedSize === s
                    ? 'border-[#CBAC70] bg-[#CBAC70] text-[#0B132B] shadow-md scale-102 font-black'
                    : 'border-white/15 bg-[#0B132B] text-[#94A3B8] hover:text-white hover:border-[#CBAC70]/50'
                }`}
              >
                {s}
              </button>
            ))}
          </div>
        </div>

        {/* 3. Quantity Stepper */}
        <div className="space-y-2 pt-2 border-t border-white/10">
          <span className="font-bold text-xs text-[#FDFCFF] block">Jumlah Pesanan:</span>
          <div className="flex items-center gap-3">
            <div className="flex items-center border border-white/20 rounded-xl bg-[#0B132B] overflow-hidden">
              <button
                type="button"
                onClick={() => handleQuantity(-1)}
                disabled={quantity <= 1}
                className="w-10 h-10 flex items-center justify-center font-bold text-base text-[#94A3B8] hover:text-white hover:bg-white/5 transition disabled:opacity-30 cursor-pointer"
              >
                -
              </button>
              <span className="w-12 h-10 flex items-center justify-center text-center font-black text-sm text-[#FDFCFF] font-mono">
                {quantity}
              </span>
              <button
                type="button"
                onClick={() => handleQuantity(1)}
                disabled={quantity >= stockAvailable}
                className="w-10 h-10 flex items-center justify-center font-bold text-base text-[#94A3B8] hover:text-white hover:bg-white/5 transition disabled:opacity-30 cursor-pointer"
              >
                +
              </button>
            </div>

            <span className="text-[11px] text-[#94A3B8] font-mono">
              Maks. {stockAvailable} pcs
            </span>
          </div>
        </div>

        {/* 4. Desktop Action Buttons */}
        <div className="hidden sm:flex items-center gap-3 pt-4 border-t border-white/10">
          <button
            type="button"
            onClick={() => toggleWishlist(product.id)}
            className={`p-3.5 rounded-xl border transition-all active:scale-95 shadow flex items-center justify-center shrink-0 cursor-pointer ${
              isCurrentProductFavorited
                ? 'bg-rose-500/20 border-rose-500/60 text-rose-400'
                : 'bg-[#14204A] border-[#CBAC70]/40 text-slate-300 hover:text-rose-400'
            }`}
            title="Simpan ke Wishlist (Cache)"
          >
            <Heart
              className={`w-5 h-5 ${
                isCurrentProductFavorited ? 'fill-rose-500 text-rose-500' : ''
              }`}
            />
          </button>

          <button
            type="button"
            onClick={handleAddToBag}
            className="flex-1 py-3.5 rounded-xl bg-[#14204A] hover:bg-[#1A2A5E] border border-[#CBAC70]/40 text-[#CBAC70] font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all active:scale-95 shadow cursor-pointer"
          >
            <ShoppingBag className="w-4 h-4" />
            <span>Add to Bag</span>
          </button>

          <button
            type="button"
            onClick={handleInstantBuy}
            className="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 transition-all active:scale-95 shadow-xl cursor-pointer"
          >
            <Zap className="w-4 h-4 fill-current" />
            <span>Instant Buy</span>
          </button>
        </div>
      </div>
    </div>
  );
}
