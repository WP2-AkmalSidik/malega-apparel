import React from 'react';
import { katalogCollections } from '../../../data/katalog';
import { fetchProductsFromApi } from '../../../lib/api';
import KatalogDetailClient from './KatalogDetailClient';

export const revalidate = 0;

export async function generateStaticParams() {
  return katalogCollections.flatMap((c) => [
    { slug: c.slug },
    { slug: c.id }
  ]);
}

export default async function KatalogDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const resolvedParams = await params;
  const products = await fetchProductsFromApi({ perPage: 100 });

  return <KatalogDetailClient slug={resolvedParams.slug} initialProducts={products} />;
}
