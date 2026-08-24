'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ShoppingBag, Star, Sparkles } from 'lucide-react';
import { Product } from '../types';
import { useCart } from '../context/CartContext';

interface ProductCardProps {
  product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
  const { addToCart } = useCart();
  const [selectedColor, setSelectedColor] = useState(product.colors[0]);

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const handleQuickAdd = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    addToCart({
      productId: product.id,
      slug: product.slug,
      title: product.title,
      color: selectedColor.name,
      size: product.sizes[0] || 'L',
      price: product.price,
      originalPrice: product.originalPrice,
      quantity: 1,
      image: selectedColor.image
    });
  };

  return (
    /* OUTER CARD (First Layer of Packaging) */
    <div className="group relative rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-1.5 sm:p-2.5 border border-[#CBAC70]/20 hover:border-[#CBAC70]/60 transition-all duration-300 shadow-xl hover:shadow-[#CBAC70]/10 flex flex-col justify-between">
      
      {/* Subtle Outer Glow & Corner Accents */}
      <div className="absolute top-1.5 left-1.5 w-2 h-2 border-t border-l border-[#CBAC70]/40 rounded-tl pointer-events-none" />
      <div className="absolute top-1.5 right-1.5 w-2 h-2 border-t border-r border-[#CBAC70]/40 rounded-tr pointer-events-none" />
      <div className="absolute bottom-1.5 left-1.5 w-2 h-2 border-b border-l border-[#CBAC70]/40 rounded-bl pointer-events-none" />
      <div className="absolute bottom-1.5 right-1.5 w-2 h-2 border-b border-r border-[#CBAC70]/40 rounded-br pointer-events-none" />

      {/* INNER CARD (Second Layer of Packaging) */}
      <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 overflow-hidden flex flex-col justify-between h-full">
        
        {/* Visual Product Image Container */}
        <Link 
          href={`/products/${product.id}`} 
          className="relative aspect-[4/5] bg-[#050914] overflow-hidden block group-hover:opacity-95 transition-opacity"
        >
          <img
            src={selectedColor.image}
            alt={product.title}
            className="w-full h-full object-cover group-hover:scale-106 transition-transform duration-500 ease-out"
          />

          {/* Gradient shadow overlay */}
          <div className="absolute inset-0 bg-gradient-to-t from-[#070D1F] via-transparent to-black/20 opacity-70" />

          {/* Top Badges */}
          <div className="absolute top-2 left-2 flex flex-col gap-1 z-10">
            {product.isNewDrop && (
              <span className="bg-[#CBAC70] text-[#0B132B] text-[8px] sm:text-[9px] font-black tracking-widest uppercase px-1.5 sm:px-2 py-0.5 rounded shadow">
                NEW
              </span>
            )}
            {product.isBestSeller && (
              <span className="bg-[#0B132B]/85 border border-[#CBAC70]/40 text-[#CBAC70] text-[8px] sm:text-[9px] font-bold tracking-wider uppercase px-1.5 sm:px-2 py-0.5 rounded shadow backdrop-blur-md">
                TOP
              </span>
            )}
          </div>

          {/* Discount Badge */}
          {product.discountPercentage > 0 && (
            <div className="absolute top-2 right-2 bg-[#0B132B]/90 border border-[#CBAC70]/60 text-[#CBAC70] text-[9px] sm:text-[10px] font-black px-1.5 py-0.2 rounded shadow">
              -{product.discountPercentage}%
            </div>
          )}

          {/* GSM & Fit Pill Bottom Left */}
          <div className="absolute bottom-2 left-2 z-10">
            <span className="text-[9px] sm:text-[10px] font-bold text-[#FDFCFF] bg-[#0B132B]/90 border border-[#CBAC70]/30 backdrop-blur-md px-2 py-0.5 rounded-md">
              {product.gsm ? `${product.gsm}GSM` : product.material.split(' ')[0]}
            </span>
          </div>
        </Link>

        {/* Inner Content Details */}
        <div className="p-3 sm:p-4 flex flex-col justify-between flex-1 space-y-2.5">
          
          <div className="space-y-1.5">
            {/* Colorway Swatches on Mobile & Desktop */}
            <div className="flex items-center gap-1.5">
              {product.colors.map((c, idx) => (
                <button
                  key={idx}
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    setSelectedColor(c);
                  }}
                  className={`w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full border transition-all ${
                    selectedColor.name === c.name
                      ? 'border-[#CBAC70] ring-1.5 ring-[#CBAC70] scale-110'
                      : 'border-white/30 hover:border-white/80'
                  }`}
                  style={{ backgroundColor: c.hex }}
                  title={c.name}
                />
              ))}
              <span className="text-[9px] sm:text-[10px] text-[#94A3B8] ml-auto font-medium">
                {product.colors.length} warna
              </span>
            </div>

            {/* Title */}
            <Link href={`/products/${product.id}`} className="block">
              <h3 className="text-xs sm:text-sm font-bold text-[#FDFCFF] group-hover:text-[#CBAC70] transition-colors line-clamp-1 leading-snug">
                {product.title}
              </h3>
              <p className="text-[10px] sm:text-[11px] text-[#94A3B8] line-clamp-1 mt-0.5">
                {product.subtitle}
              </p>
            </Link>
          </div>

          {/* Pricing & Fast Buy Action */}
          <div className="pt-2 border-t border-white/10 flex items-center justify-between gap-1">
            <div className="min-w-0">
              {product.originalPrice > product.price && (
                <span className="text-[9px] sm:text-[10px] text-[#94A3B8] line-through block leading-none truncate">
                  {formatRupiah(product.originalPrice)}
                </span>
              )}
              <span className="text-xs sm:text-sm font-black text-[#CBAC70] gold-gradient-pure block leading-tight truncate">
                {formatRupiah(product.price)}
              </span>
            </div>

            {/* Quick Add Button */}
            <button
              onClick={handleQuickAdd}
              className="p-2 sm:p-2.5 rounded-lg sm:rounded-xl bg-[#14204A] hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] border border-[#CBAC70]/40 transition-all duration-200 shadow active:scale-90 shrink-0"
              title="Add to Bag"
              aria-label="Add to Bag"
            >
              <ShoppingBag className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            </button>
          </div>

        </div>

      </div>

    </div>
  );
}
