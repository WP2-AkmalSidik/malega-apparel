import React from 'react';
import Link from 'next/link';
import { Product } from '../../../../types';
import ProductCard from '../../../../components/ProductCard';

interface RelatedProductsProps {
  relatedProducts: Product[];
}

export default function RelatedProducts({ relatedProducts }: RelatedProductsProps) {
  if (relatedProducts.length === 0) return null;

  return (
    <div className="space-y-4">
      <div className="border-b border-white/10 pb-3 flex items-center justify-between">
        <h3 className="font-bold text-base sm:text-lg text-[#FDFCFF] uppercase tracking-wide">
          Koleksi Terkait Lainnya
        </h3>
        <Link href="/" className="text-xs font-bold text-[#CBAC70] hover:underline">
          Jelajahi Semua Koleksi →
        </Link>
      </div>

      <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-5">
        {relatedProducts.map((p) => (
          <ProductCard key={p.id} product={p} />
        ))}
      </div>
    </div>
  );
}
