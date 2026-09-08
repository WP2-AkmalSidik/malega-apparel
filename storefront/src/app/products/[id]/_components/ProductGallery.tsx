'use client';

import React from 'react';
import { Heart } from 'lucide-react';
import { ColorOption, Product } from '../../../../types';

interface ProductGalleryProps {
  product: Product;
  activeImage: string;
  setActiveImage: (img: string) => void;
  selectedColor: ColorOption;
  sku: string;
  isCurrentProductFavorited: boolean;
  toggleWishlist: (id: string) => void;
}

export default function ProductGallery({
  product,
  activeImage,
  setActiveImage,
  selectedColor,
  sku,
  isCurrentProductFavorited,
  toggleWishlist,
}: ProductGalleryProps) {
  return (
    <div className="lg:col-span-6 space-y-4">
      <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/30 shadow-2xl relative">
        {/* Primary Visual Picture */}
        <div className="relative aspect-[4/5] rounded-xl sm:rounded-2xl overflow-hidden bg-[#050914] border border-white/10">
          <img
            src={activeImage}
            alt={product.title}
            className="w-full h-full object-cover transition-all duration-300"
          />

          {/* Badges Overlay */}
          <div className="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
            <span className="bg-[#CBAC70] text-[#0B132B] text-[9px] sm:text-[10px] font-black tracking-widest uppercase px-2.5 py-1 rounded-md shadow-lg">
              {product.badge || 'SS26 DROP'}
            </span>
            {product.discountPercentage > 0 && (
              <span className="bg-[#0B132B]/90 border border-[#CBAC70]/60 text-[#CBAC70] text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded shadow">
                -{product.discountPercentage}% OFF
              </span>
            )}
          </div>

          {/* Wishlist Heart Button on Image */}
          <div className="absolute top-3 right-3 z-10 flex items-center gap-2">
            {isCurrentProductFavorited && (
              <span className="px-2 py-1 rounded-lg bg-rose-500/20 border border-rose-500/40 text-rose-300 text-[10px] font-mono font-bold backdrop-blur-md">
                ♥ FAVORIT ANDA
              </span>
            )}
            <button
              type="button"
              onClick={() => toggleWishlist(product.id)}
              className={`p-2.5 rounded-full backdrop-blur-md transition-all cursor-pointer shadow-lg ${
                isCurrentProductFavorited
                  ? 'bg-rose-500 text-white scale-110'
                  : 'bg-black/60 text-white/80 hover:text-rose-400 hover:bg-black/80'
              }`}
              title="Simpan ke Wishlist (Cache)"
            >
              <Heart
                className={`w-4 h-4 ${isCurrentProductFavorited ? 'fill-white' : ''}`}
              />
            </button>
          </div>

          {/* Active Color Info Tag */}
          <div className="absolute bottom-3 left-3 bg-[#080E20]/90 border border-[#CBAC70]/30 backdrop-blur-md px-3 py-1 rounded-lg text-xs font-mono text-[#CBAC70] flex items-center gap-2">
            <span
              className="w-2.5 h-2.5 rounded-full"
              style={{ backgroundColor: selectedColor.hex }}
            />
            <span>Warna: {selectedColor.name}</span>
            <span className="text-slate-400">• SKU: {sku}</span>
          </div>
        </div>

        {/* Thumbnail Gallery Row */}
        <div className="grid grid-cols-4 gap-2 sm:gap-3 pt-3">
          {product.gallery.map((img, idx) => (
            <button
              type="button"
              key={idx}
              onClick={() => setActiveImage(img)}
              className={`aspect-square rounded-xl overflow-hidden border transition-all cursor-pointer ${
                activeImage === img
                  ? 'border-[#CBAC70] ring-2 ring-[#CBAC70] scale-102'
                  : 'border-white/10 hover:border-white/40 opacity-70 hover:opacity-100'
              }`}
            >
              <img
                src={img}
                alt={`Gallery ${idx}`}
                className="w-full h-full object-cover"
              />
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}
