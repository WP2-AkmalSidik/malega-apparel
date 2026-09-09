'use client';

import React from 'react';
import { Product } from '../../../types';
import { useProductDetail } from './_hooks/useProductDetail';
import ProductBreadcrumb from './_components/ProductBreadcrumb';
import ProductGallery from './_components/ProductGallery';
import ProductStudio from './_components/ProductStudio';
import ProductSpecifications from './_components/ProductSpecifications';
import ProductReviews from './_components/ProductReviews';
import RelatedProducts from './_components/RelatedProducts';
import MobileBottomBar from './_components/MobileBottomBar';
import SizeChartModal from './_components/SizeChartModal';

interface ProductDetailClientProps {
  productId: string;
  initialProduct?: Product | null;
  allProducts?: Product[];
}

export default function ProductDetailClient({
  productId,
  initialProduct,
  allProducts,
}: ProductDetailClientProps) {
  const {
    product,
    selectedColor,
    selectedSize,
    quantity,
    activeImage,
    setActiveImage,
    showSizeChart,
    setShowSizeChart,
    reviewFilter,
    setReviewFilter,
    activeVariant,
    currentPrice,
    currentCompareAt,
    handleColorChange,
    handleSizeChange,
    handleQuantity,
    handleAddToBag,
    handleInstantBuy,
    reviewsList,
    filteredReviews,
    relatedProducts,
    isCurrentProductFavorited,
    toggleWishlist,
    isNumericSizeProduct,
    isAllSizeProduct,
  } = useProductDetail({ productId, initialProduct, allProducts });

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8 sm:space-y-12">
      {/* Breadcrumb Navigation */}
      <ProductBreadcrumb title={product.title} />

      {/* Main Product Showcase Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        {/* Left Column: Gallery & Lookbook Pictures */}
        <ProductGallery
          product={product}
          activeImage={activeImage}
          setActiveImage={setActiveImage}
          selectedColor={selectedColor}
          sku={activeVariant.sku}
          isCurrentProductFavorited={isCurrentProductFavorited}
          toggleWishlist={toggleWishlist}
        />

        {/* Right Column: Product Specifications & Action Studio */}
        <ProductStudio
          product={product}
          selectedColor={selectedColor}
          handleColorChange={handleColorChange}
          selectedSize={selectedSize}
          handleSizeChange={handleSizeChange}
          quantity={quantity}
          handleQuantity={handleQuantity}
          activeVariant={activeVariant}
          currentPrice={currentPrice}
          currentCompareAt={currentCompareAt}
          setShowSizeChart={setShowSizeChart}
          isCurrentProductFavorited={isCurrentProductFavorited}
          toggleWishlist={toggleWishlist}
          handleAddToBag={handleAddToBag}
          handleInstantBuy={handleInstantBuy}
        />
      </div>

      {/* Specifications & Description */}
      <ProductSpecifications
        specifications={product.specifications}
        description={product.description}
      />

      {/* Verified Reviews Section */}
      <ProductReviews
        productId={product.id}
        productName={product.title}
        reviewCount={product.reviewCount}
      />

      {/* Related Products Grid */}
      <RelatedProducts relatedProducts={relatedProducts} />

      {/* Mobile Sticky Bottom Floating Bar */}
      <MobileBottomBar
        productId={product.id}
        isCurrentProductFavorited={isCurrentProductFavorited}
        toggleWishlist={toggleWishlist}
        handleAddToBag={handleAddToBag}
        handleInstantBuy={handleInstantBuy}
        currentPrice={currentPrice}
        quantity={quantity}
      />

      {/* Dynamic Size Chart Modal */}
      <SizeChartModal
        showSizeChart={showSizeChart}
        setShowSizeChart={setShowSizeChart}
        isNumericSizeProduct={isNumericSizeProduct}
        isAllSizeProduct={isAllSizeProduct}
        sizes={product.sizes}
        selectedSize={selectedSize}
        setSelectedSize={handleSizeChange}
      />
    </div>
  );
}
