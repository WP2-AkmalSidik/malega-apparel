'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ShoppingBag, Eye, Star, Sparkles } from 'lucide-react';
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
    <div className="group luxury-card rounded-2xl overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-2xl hover:border-[#CBAC70]/60 relative">
      
      {/* Product Image Container */}
      <Link href={`/products/${product.id}`} className="relative aspect-[3/4] bg-[#070D1F] overflow-hidden block">
        <img
          src={selectedColor.image}
          alt={product.title}
          className="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700 ease-out"
        />

        {/* Dark Gradient Overlay on hover */}
        <div className="absolute inset-0 bg-gradient-to-t from-[#0B132B] via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity" />

        {/* Badges Top Left & Right */}
        <div className="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
          {product.isNewDrop && (
            <span className="bg-[#CBAC70] text-[#0B132B] text-[10px] font-black tracking-widest uppercase px-2.5 py-0.5 rounded shadow">
              NEW DROP
            </span>
          )}
          {product.isBestSeller && (
            <span className="bg-[#1C2541] border border-[#CBAC70]/40 text-[#CBAC70] text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded shadow backdrop-blur-md">
              BESTSELLER
            </span>
          )}
        </div>

        {product.discountPercentage > 0 && (
          <div className="absolute top-3 right-3 bg-[#0B132B]/90 border border-[#CBAC70]/50 text-[#CBAC70] text-[11px] font-black px-2 py-0.5 rounded shadow">
            -{product.discountPercentage}%
          </div>
        )}

        {/* GSM & Fit Pill Bottom Left */}
        <div className="absolute bottom-3 left-3 z-10">
          <span className="text-[10px] font-bold text-[#FDFCFF] bg-[#0B132B]/80 border border-white/10 backdrop-blur-md px-2.5 py-1 rounded-full">
            {product.gsm ? `${product.gsm}GSM` : product.material}
          </span>
        </div>

        {/* Quick View Button overlay on hover */}
        <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px]">
          <span className="px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs flex items-center gap-1.5 shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-transform">
            <Eye className="w-3.5 h-3.5" /> View Details
          </span>
        </div>
      </Link>

      {/* Card Info Content */}
      <div className="p-5 flex flex-col justify-between flex-1 space-y-4">
        
        <div className="space-y-2">
          {/* Colorway Swatches */}
          <div className="flex items-center gap-1.5">
            {product.colors.map((c, idx) => (
              <button
                key={idx}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  setSelectedColor(c);
                }}
                className={`w-4 h-4 rounded-full border transition-all ${
                  selectedColor.name === c.name
                    ? 'border-[#CBAC70] ring-2 ring-[#CBAC70]/40 scale-110'
                    : 'border-white/30 hover:border-white/80'
                }`}
                style={{ backgroundColor: c.hex }}
                title={c.name}
              />
            ))}
            <span className="text-[10px] text-[#94A3B8] ml-1 font-medium">{product.colors.length} Colors</span>
          </div>

          {/* Title */}
          <Link href={`/products/${product.id}`} className="block">
            <h3 className="text-sm sm:text-base font-bold text-[#FDFCFF] hover:text-[#CBAC70] transition-colors line-clamp-1">
              {product.title}
            </h3>
            <p className="text-xs text-[#94A3B8] line-clamp-1 mt-0.5">
              {product.subtitle}
            </p>
          </Link>
        </div>

        {/* Pricing & Add to Bag */}
        <div className="pt-3 border-t border-white/10 flex items-center justify-between">
          <div>
            {product.originalPrice > product.price && (
              <span className="text-[11px] text-[#94A3B8] line-through block leading-none">
                {formatRupiah(product.originalPrice)}
              </span>
            )}
            <span className="text-base sm:text-lg font-black text-[#CBAC70] gold-gradient-pure block mt-0.5">
              {formatRupiah(product.price)}
            </span>
          </div>

          <button
            onClick={handleQuickAdd}
            className="p-2.5 rounded-xl bg-[#1C2541] hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] border border-[#CBAC70]/40 transition-all duration-200 shadow-sm active:scale-95 group/btn"
            title="Quick Add to Bag"
          >
            <ShoppingBag className="w-4 h-4" />
          </button>
        </div>

      </div>

    </div>
  );
}
