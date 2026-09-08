'use client';

import { useState, useMemo } from 'react';
import { Product } from '../../types';

export function useProductFilter(initialProducts: Product[]) {
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedSize, setSelectedSize] = useState<string>('all');
  const [sortBy, setSortBy] = useState<string>('featured');
  const [onlyNewDrops, setOnlyNewDrops] = useState<boolean>(false);
  const [onlyBestSellers, setOnlyBestSellers] = useState<boolean>(false);
  const [isFilterModalOpen, setIsFilterModalOpen] = useState<boolean>(false);

  const categories = useMemo(() => {
    const defaultLabels: Record<string, string> = {
      'T-Shirts': 'Heavyweight Tees (300GSM)',
      'Kemeja & Atasan': 'Kemeja & Atasan',
      Outerwear: 'Hoodies & Outer',
      'Outerwear & Jaket': 'Outerwear & Jaket',
      Bottoms: 'Cargo & Denim',
      'Celana & Chino': 'Celana & Chino',
      Accessories: 'Caps & Bags',
      'Aksesoris & Kulit': 'Aksesoris & Kulit',
      'Heritage Collection': 'Heritage Collection',
    };

    const uniqueCategories = Array.from(
      new Set(initialProducts.map((p) => p.category).filter(Boolean))
    );

    return [
      { id: 'all', label: 'Semua Koleksi' },
      ...uniqueCategories.map((cat) => ({
        id: cat,
        label: defaultLabels[cat] || cat,
      })),
    ];
  }, [initialProducts]);

  const sizeOptions = useMemo(() => {
    const set = new Set<string>();
    initialProducts.forEach((p) => (p.sizes || []).forEach((s) => set.add(s)));
    const sorted = Array.from(set).sort((a, b) => {
      const aNum = parseInt(a);
      const bNum = parseInt(b);
      if (!isNaN(aNum) && !isNaN(bNum)) return aNum - bNum;
      return a.localeCompare(b);
    });
    return ['all', ...sorted];
  }, [initialProducts]);

  const filteredProducts = useMemo(() => {
    return initialProducts
      .filter((p) => {
        // Category filter
        if (selectedCategory !== 'all' && p.category !== selectedCategory) {
          return false;
        }
        // Search filter
        if (searchQuery.trim() !== '') {
          const q = searchQuery.toLowerCase();
          const matchTitle = p.title.toLowerCase().includes(q);
          const matchSubtitle = (p.subtitle || '').toLowerCase().includes(q);
          const matchDesc = (p.description || '').toLowerCase().includes(q);
          const matchCat = (p.category || '').toLowerCase().includes(q);
          const matchMaterial = (p.material || '').toLowerCase().includes(q);
          if (!matchTitle && !matchSubtitle && !matchDesc && !matchCat && !matchMaterial)
            return false;
        }
        // Size filter
        if (selectedSize !== 'all' && !(p.sizes || []).includes(selectedSize)) {
          return false;
        }
        // New drops
        if (onlyNewDrops && !p.isNewDrop) {
          return false;
        }
        // Best sellers
        if (onlyBestSellers && !p.isBestSeller) {
          return false;
        }
        return true;
      })
      .sort((a, b) => {
        if (sortBy === 'price-low') return a.price - b.price;
        if (sortBy === 'price-high') return b.price - a.price;
        if (sortBy === 'sold') return (b.soldCount || 0) - (a.soldCount || 0);
        if (sortBy === 'rating') return (b.rating || 0) - (a.rating || 0);
        return 0; // default featured
      });
  }, [
    initialProducts,
    selectedCategory,
    searchQuery,
    selectedSize,
    onlyNewDrops,
    onlyBestSellers,
    sortBy,
  ]);

  const activeFilterCount = useMemo(() => {
    let count = 0;
    if (selectedSize !== 'all') count++;
    if (onlyNewDrops) count++;
    if (onlyBestSellers) count++;
    if (sortBy !== 'featured') count++;
    return count;
  }, [selectedSize, onlyNewDrops, onlyBestSellers, sortBy]);

  const handleResetFilters = () => {
    setSelectedCategory('all');
    setSearchQuery('');
    setSelectedSize('all');
    setSortBy('featured');
    setOnlyNewDrops(false);
    setOnlyBestSellers(false);
  };

  return {
    selectedCategory,
    setSelectedCategory,
    searchQuery,
    setSearchQuery,
    selectedSize,
    setSelectedSize,
    sortBy,
    setSortBy,
    onlyNewDrops,
    setOnlyNewDrops,
    onlyBestSellers,
    setOnlyBestSellers,
    isFilterModalOpen,
    setIsFilterModalOpen,
    categories,
    sizeOptions,
    filteredProducts,
    activeFilterCount,
    handleResetFilters,
  };
}
