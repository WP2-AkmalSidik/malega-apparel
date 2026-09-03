import React from 'react';
import { productsCatalog } from '../../../data/products';
import ProductDetailClient from './ProductDetailClient';

export function generateStaticParams() {
  return productsCatalog.flatMap((p) => [
    { id: p.id },
    { id: p.slug }
  ]);
}

export default async function ProductDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const resolvedParams = await params;
  return <ProductDetailClient productId={resolvedParams.id} />;
}
