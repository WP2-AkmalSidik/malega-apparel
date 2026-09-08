'use client';

import React from 'react';
import { Product } from '../../types';
import ProductCard from '../../components/ProductCard';
import { useProductFilter } from '../_hooks/useProductFilter';
import FilterBar from './FilterBar';
import FilterModal from './FilterModal';

interface StoreHomeClientProps {
  initialProducts: Product[];
}

export default function StoreHomeClient({ initialProducts }: StoreHomeClientProps) {
  const {
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
  } = useProductFilter(initialProducts);

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-5 space-y-3.5">
      <FilterBar
        initialProducts={initialProducts}
        searchQuery={searchQuery}
        setSearchQuery={setSearchQuery}
        sortBy={sortBy}
        setSortBy={setSortBy}
        activeFilterCount={activeFilterCount}
        setIsFilterModalOpen={setIsFilterModalOpen}
        categories={categories}
        selectedCategory={selectedCategory}
        setSelectedCategory={setSelectedCategory}
        selectedSize={selectedSize}
        setSelectedSize={setSelectedSize}
        onlyNewDrops={onlyNewDrops}
        setOnlyNewDrops={setOnlyNewDrops}
        onlyBestSellers={onlyBestSellers}
        setOnlyBestSellers={setOnlyBestSellers}
        handleResetFilters={handleResetFilters}
      />

      {/* Product Catalog Listing */}
      <section className="space-y-2.5 pt-1">
        <div className="flex items-center justify-between text-[11px] text-[#94A3B8] px-0.5">
          <span>
            Menampilkan <strong className="text-[#CBAC70] font-bold">{filteredProducts.length}</strong>{' '}
            produk artikel
          </span>
          <span className="text-[#CBAC70] font-mono text-[10px]">100% Bespoke Studio</span>
        </div>

        {filteredProducts.length === 0 ? (
          <div className="rounded-2xl bg-[#0E1736] border border-white/10 p-8 text-center space-y-3">
            <p className="text-sm font-bold text-[#FDFCFF]">Tidak ada produk yang sesuai filter</p>
            <p className="text-xs text-[#94A3B8]">Coba sesuaikan kata kunci atau reset filter Anda.</p>
            <button
              onClick={handleResetFilters}
              className="px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase cursor-pointer"
            >
              Reset Filter
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 lg:gap-5">
            {filteredProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}
      </section>

      <FilterModal
        isOpen={isFilterModalOpen}
        onClose={() => setIsFilterModalOpen(false)}
        sortBy={sortBy}
        setSortBy={setSortBy}
        sizeOptions={sizeOptions}
        selectedSize={selectedSize}
        setSelectedSize={setSelectedSize}
        onlyNewDrops={onlyNewDrops}
        setOnlyNewDrops={setOnlyNewDrops}
        onlyBestSellers={onlyBestSellers}
        setOnlyBestSellers={setOnlyBestSellers}
        handleResetFilters={handleResetFilters}
        filteredProductsCount={filteredProducts.length}
      />
    </div>
  );
}
