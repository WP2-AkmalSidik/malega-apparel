import React from 'react';
import { katalogCollections } from '../../../data/katalog';
import KatalogDetailClient from './KatalogDetailClient';

export function generateStaticParams() {
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
  return <KatalogDetailClient slug={resolvedParams.slug} />;
}
