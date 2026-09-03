import React from 'react';
import { fetchProductsFromApi } from '../lib/api';
import StoreHomeClient from './StoreHomeClient';

export const revalidate = 0;

export default async function StoreHomePage() {
  const products = await fetchProductsFromApi({ perPage: 100 });

  return <StoreHomeClient initialProducts={products} />;
}
