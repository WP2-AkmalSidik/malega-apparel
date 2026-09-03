import React from 'react';
import { productsCatalog } from '../../../data/products';
import { fetchProductDetailFromApi, fetchProductsFromApi } from '../../../lib/api';
import ProductDetailClient from './ProductDetailClient';

export const revalidate = 0;

export async function generateStaticParams() {
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
  const [product, allProducts] = await Promise.all([
    fetchProductDetailFromApi(resolvedParams.id),
    fetchProductsFromApi({ perPage: 100 }),
  ]);

  return (
    <ProductDetailClient
      productId={resolvedParams.id}
      initialProduct={product}
      allProducts={allProducts}
    />
  );
}
