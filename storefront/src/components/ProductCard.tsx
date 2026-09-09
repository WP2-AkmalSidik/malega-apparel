'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ShoppingBag, Star, Sparkles, Heart } from 'lucide-react';
import { Product } from '../types';
import { useCart } from '../context/CartContext';
import { useWishlist } from '../context/WishlistContext';
import { useFlyToCart } from '../context/FlyToCartContext';

interface ProductCardProps {
  product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
  const { addToCart } = useCart();
  const { toggleWishlist, isInWishlist } = useWishlist();
  const { triggerFly } = useFlyToCart();

  const fallbackImg = product.gallery?.[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80';
  
  const safeColors = (product.colors && product.colors.length > 0)
    ? product.colors
    : [{ name: 'Signature', hex: '#0B132B', image: fallbackImg }];

  const [selectedColor, setSelectedColor] = useState(safeColors[0]);

  const activeImage = selectedColor?.image || fallbackImg;
  const isFavorited = isInWishlist(product.id) || isInWishlist(product.slug);

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const handleQuickAdd = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();

    let startX = typeof window !== 'undefined' ? window.innerWidth / 2 : 200;
    let startY = typeof window !== 'undefined' ? window.innerHeight / 2 : 200;

    if (e.currentTarget) {
      const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
      startX = rect.left + rect.width / 2;
      startY = rect.top + rect.height / 2;
    }

    triggerFly(activeImage, startX, startY);

    addToCart({
      productId: product.id,
      slug: product.slug,
      title: product.title,
      color: selectedColor?.name || 'Signature',
      size: (product.sizes && product.sizes.length > 0) ? product.sizes[0] : 'All Size',
      price: product.price,
      originalPrice: product.originalPrice,
      quantity: 1,
      image: activeImage
    });
  };

  return (
    /* OUTER CARD (Bespoke Frame - "Di dalam card") */
    <div className="group relative rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-1.5 sm:p-2.5 border border-[#CBAC70]/25 hover:border-[#CBAC70]/60 transition-all duration-300 shadow-xl hover:shadow-[#CBAC70]/10 flex flex-col justify-between">
      
      {/* Subtle Corner Accents (Signature Streetwear Malega) */}
      <div className="absolute top-1.5 left-1.5 w-2 h-2 border-t border-l border-[#CBAC70]/40 rounded-tl pointer-events-none" />
      <div className="absolute top-1.5 right-1.5 w-2 h-2 border-t border-r border-[#CBAC70]/40 rounded-tr pointer-events-none" />
      <div className="absolute bottom-1.5 left-1.5 w-2 h-2 border-b border-l border-[#CBAC70]/40 rounded-bl pointer-events-none" />
      <div className="absolute bottom-1.5 right-1.5 w-2 h-2 border-b border-r border-[#CBAC70]/40 rounded-br pointer-events-none" />

      {/* INNER CARD (Nested Content Container) */}
      <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 overflow-hidden flex flex-col justify-between h-full">
        
        {/* Visual Product Image Container (Slightly squarish aspect-[6/7], but not strictly square) */}
        <Link 
          href={`/products/${product.slug}`} 
          className="relative aspect-[6/7] bg-[#050914] overflow-hidden block group-hover:opacity-95 transition-opacity"
        >
          <img
            src={activeImage}
            alt={product.title}
            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
          />

          {/* Subtle gradient vignette overlay */}
          <div className="absolute inset-0 bg-gradient-to-t from-[#070D1F] via-transparent to-black/20 opacity-60 pointer-events-none" />

          {/* Top Badges (Strictly max 1 status on left) */}
          <div className="absolute top-2.5 left-2.5 z-10">
            {product.isNewDrop ? (
              <span className="bg-[#CBAC70] text-[#0B132B] text-[9px] font-black tracking-wider uppercase px-2 py-0.5 rounded shadow">
                BARU
              </span>
            ) : product.isBestSeller ? (
              <span className="bg-[#0B132B]/90 border border-[#CBAC70]/40 text-[#CBAC70] text-[9px] font-bold tracking-wider uppercase px-2 py-0.5 rounded shadow backdrop-blur-md">
                TERLARIS
              </span>
            ) : null}
          </div>

          {/* Discount & Wishlist Heart Button Top Right */}
          <div className="absolute top-2.5 right-2.5 flex items-center gap-1.5 z-10">
            {product.discountPercentage > 0 && (
              <div className="bg-[#0B132B]/90 border border-[#CBAC70]/50 text-[#CBAC70] text-[9.5px] font-black px-1.5 py-0.5 rounded shadow backdrop-blur-md">
                -{product.discountPercentage}%
              </div>
            )}
            <button
              type="button"
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                toggleWishlist(product.id, product);
              }}
              className={`w-8 h-8 rounded-full backdrop-blur-md transition-all cursor-pointer flex items-center justify-center ${
                isFavorited
                  ? 'bg-rose-500 text-white shadow-md scale-105'
                  : 'bg-black/50 text-white/80 hover:text-rose-400 hover:bg-black/70'
              }`}
              title="Tambah ke Favorit"
              aria-label="Simpan ke Favorit"
            >
              <Heart className={`w-4 h-4 ${isFavorited ? 'fill-white' : ''}`} />
            </button>
          </div>
        </Link>

        {/* Content Details */}
        <div className="p-3 sm:p-4 flex flex-col justify-between flex-1 space-y-2.5">
          
          <div className="space-y-1.5">
            {/* Colorway Swatches & Fabric Spec Metadata */}
            <div className="flex items-center justify-between gap-1">
              <div className="flex items-center gap-1.5">
                {safeColors.map((c, idx) => (
                  <button
                    type="button"
                    key={idx}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      setSelectedColor(c);
                    }}
                    className={`w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full border transition-all cursor-pointer ${
                      selectedColor?.name === c.name
                        ? 'border-[#CBAC70] ring-2 ring-[#CBAC70] scale-110'
                        : 'border-white/30 hover:border-white/70'
                    }`}
                    style={{ backgroundColor: c.hex }}
                    title={c.name}
                    aria-label={c.name}
                  />
                ))}
                <span className="text-[10px] text-[#94A3B8] ml-1 font-medium">
                  {safeColors.length} warna
                </span>
              </div>

              {/* Subtle Material / GSM info moved from badge to secondary spec */}
              <span className="text-[10px] text-[#94A3B8] font-mono">
                {product.gsm ? `${product.gsm} GSM` : (product.material ? product.material.split(' ')[0] : '')}
              </span>
            </div>

            {/* Title */}
            <Link href={`/products/${product.slug}`} className="block group/link">
              <h3 className="text-xs sm:text-sm font-bold text-[#FDFCFF] group-hover/link:text-[#CBAC70] transition-colors line-clamp-1 leading-snug">
                {product.title}
              </h3>
              <p className="text-[10px] sm:text-[11px] text-[#94A3B8] line-clamp-1 mt-0.5">
                {product.subtitle}
              </p>
            </Link>
          </div>

          {/* Pricing & Fast Buy Action */}
          <div className="pt-2 border-t border-white/5 flex items-center justify-between gap-2">
            <div className="min-w-0">
              {product.originalPrice > product.price && (
                <span className="text-[10px] text-[#94A3B8] line-through block leading-none truncate">
                  {formatRupiah(product.originalPrice)}
                </span>
              )}
              <span className="text-xs sm:text-sm font-black text-[#CBAC70] gold-gradient-pure block leading-tight truncate">
                {formatRupiah(product.price)}
              </span>
            </div>

            {/* Quick Add Button with Accessible Touch Target */}
            <button
              type="button"
              onClick={handleQuickAdd}
              className="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#14204A] hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] border border-[#CBAC70]/30 hover:border-[#CBAC70] transition-all duration-200 shadow-sm active:scale-95 shrink-0 flex items-center justify-center cursor-pointer"
              title="Tambah ke Keranjang"
              aria-label="Tambah ke Keranjang"
            >
              <ShoppingBag className="w-4 h-4" />
            </button>
          </div>

        </div>

      </div>

    </div>
  );
}
