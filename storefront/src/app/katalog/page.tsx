import React from 'react';
import { fetchCollectionsFromApi } from '../../lib/api';
import KatalogIndexClient from './KatalogIndexClient';

export const revalidate = 0;

export default async function KatalogDirectoryPage() {
  const collections = await fetchCollectionsFromApi();

  return <KatalogIndexClient initialCollections={collections} />;
}
