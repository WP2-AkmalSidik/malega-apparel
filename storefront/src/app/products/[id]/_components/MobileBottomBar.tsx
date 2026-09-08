'use client';

import React from 'react';
import { Heart, ShoppingBag, Zap } from 'lucide-react';
import { formatRupiah } from '../../../../lib/utils';

interface MobileBottomBarProps {
  productId: string;
  isCurrentProductFavorited: boolean;
  toggleWishlist: (id: string) => void;
  handleAddToBag: (e?: React.MouseEvent) => void;
  handleInstantBuy: (e?: React.MouseEvent) => void;
  currentPrice: number;
  quantity: number;
}

export default function MobileBottomBar({
  productId,
  isCurrentProductFavorited,
  toggleWishlist,
  handleAddToBag,
  handleInstantBuy,
  currentPrice,
  quantity,
}: MobileBottomBarProps) {
  return (
    <div className="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#080E20]/95 backdrop-blur-xl border-t border-[#CBAC70]/30 px-3 py-2.5 flex items-center gap-2 shadow-2xl safe-bottom">
      {/* 1. Wishlist Favorite Button */}
      <button
        type="button"
        onClick={() => toggleWishlist(productId)}
        className={`w-11 h-11 rounded-xl border transition-all active:scale-95 shadow flex items-center justify-center shrink-0 cursor-pointer ${
          isCurrentProductFavorited
            ? 'bg-rose-500/20 border-rose-500/60 text-rose-400'
            : 'bg-[#14204A] border-[#CBAC70]/40 text-slate-300 hover:text-rose-400'
        }`}
        title="Wishlist (Cache)"
      >
        <Heart
          className={`w-4 h-4 ${
            isCurrentProductFavorited ? 'fill-rose-500 text-rose-500' : ''
          }`}
        />
      </button>

      {/* 2. Add to Bag Button (Compact fixed width) */}
      <button
        type="button"
        onClick={handleAddToBag}
        className="h-11 px-3.5 sm:px-4 bg-[#14204A] hover:bg-[#1A2A5E] border border-[#CBAC70]/40 text-[#CBAC70] font-black text-xs uppercase tracking-wider rounded-xl flex items-center justify-center gap-1.5 active:scale-95 shadow shrink-0 cursor-pointer whitespace-nowrap"
      >
        <ShoppingBag className="w-3.5 h-3.5 shrink-0" />
        <span>+ Bag</span>
      </button>

      {/* 3. Instant Buy Button (Takes remaining width, strictly single line) */}
      <button
        type="button"
        onClick={handleInstantBuy}
        className="flex-1 min-w-0 h-11 px-3 sm:px-4 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] font-black text-[11px] sm:text-xs uppercase tracking-tight rounded-xl flex items-center justify-center gap-1.5 shadow-lg active:scale-95 cursor-pointer whitespace-nowrap overflow-hidden"
      >
        <Zap className="w-3.5 h-3.5 fill-current shrink-0" />
        <span className="truncate whitespace-nowrap">
          Beli ({formatRupiah(currentPrice * quantity)})
        </span>
      </button>
    </div>
  );
}
