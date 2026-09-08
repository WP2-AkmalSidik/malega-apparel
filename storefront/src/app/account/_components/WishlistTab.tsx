'use client';

import React from 'react';
import Link from 'next/link';
import { Product } from '../../../types';

interface WishlistTabProps {
  wishlistProducts: Product[];
}

export default function WishlistTab({ wishlistProducts }: WishlistTabProps) {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h3 className="text-sm font-bold text-slate-200">
          Produk yang Disimpan ({wishlistProducts.length})
        </h3>
        <Link href="/favorites" className="text-xs text-[#CBAC70] hover:underline font-semibold">
          Buka Halaman Wishlist Penuh →
        </Link>
      </div>

      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
        {wishlistProducts.map((p) => (
          <Link
            key={p.id}
            href={`/products/${p.slug}`}
            className="rounded-2xl bg-[#0E1736] border border-white/10 hover:border-[#CBAC70]/40 p-3 space-y-2 group transition"
          >
            <img
              src={p.colors[0]?.image || p.gallery[0]}
              alt={p.title}
              className="w-full aspect-square rounded-xl object-cover"
            />
            <p className="font-bold text-xs text-slate-200 group-hover:text-[#CBAC70] transition line-clamp-1">
              {p.title}
            </p>
            <p className="font-mono font-bold text-xs text-[#CBAC70]">
              Rp {p.price.toLocaleString('id-ID')}
            </p>
          </Link>
        ))}
      </div>
    </div>
  );
}
