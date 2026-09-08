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
    <div className="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#080E20]/95 backdrop-blur-xl border-t border-[#CBAC70]/30 px-3 py-2.5 flex items-center gap-2 shadow-2xl">
      <button
        type="button"
        onClick={() => toggleWishlist(productId)}
        className={`p-3 rounded-xl border transition-all active:scale-95 shadow flex items-center justify-center shrink-0 cursor-pointer ${
          isCurrentProductFavorited
            ? 'bg-rose-500/20 border-rose-500/60 text-rose-400'
            : 'bg-[#14204A] border-[#CBAC70]/40 text-slate-300'
        }`}
        title="Wishlist (Cache)"
      >
        <Heart
          className={`w-4 h-4 ${
            isCurrentProductFavorited ? 'fill-rose-500 text-rose-500' : ''
          }`}
        />
      </button>

      <button
        type="button"
        onClick={handleAddToBag}
        className="flex-1 py-3 bg-[#14204A] border border-[#CBAC70]/40 text-[#CBAC70] font-black text-xs uppercase rounded-xl flex items-center justify-center gap-1.5 active:scale-95 shadow cursor-pointer"
      >
        <ShoppingBag className="w-3.5 h-3.5" />
        <span>+ Bag</span>
      </button>

      <button
        type="button"
        onClick={handleInstantBuy}
        className="flex-1 py-3 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl flex items-center justify-center gap-1.5 shadow-lg active:scale-95 cursor-pointer"
      >
        <Zap className="w-3.5 h-3.5 fill-current" />
        <span>Beli ({formatRupiah(currentPrice * quantity)})</span>
      </button>
    </div>
  );
}
