'use client';

import { useState, useEffect, useMemo } from 'react';
import { useRouter } from 'next/navigation';
import { productsCatalog } from '../../../../data/products';
import { useCart } from '../../../../context/CartContext';
import { useWishlist } from '../../../../context/WishlistContext';
import { useFlyToCart } from '../../../../context/FlyToCartContext';
import { ColorOption, Product } from '../../../../types';

interface UseProductDetailOptions {
  productId: string;
  initialProduct?: Product | null;
  allProducts?: Product[];
}

export function useProductDetail({
  productId,
  initialProduct,
  allProducts,
}: UseProductDetailOptions) {
  const router = useRouter();
  const { addToCart, instantBuy } = useCart();
  const { isInWishlist, toggleWishlist } = useWishlist();
  const { triggerFly } = useFlyToCart();

  const product = useMemo(() => {
    if (initialProduct) return initialProduct;
    const fromAll = (allProducts || []).find(
      (p) => p.id === productId || p.slug === productId
    );
    if (fromAll) return fromAll;
    return (
      productsCatalog.find((p) => p.id === productId || p.slug === productId) ||
      productsCatalog[0]
    );
  }, [initialProduct, allProducts, productId]);

  const defaultColor: ColorOption =
    product.colors && product.colors.length > 0
      ? product.colors[0]
      : {
          name: 'Signature',
          hex: '#0B132B',
          image:
            product.gallery?.[0] ||
            'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
        };

  const [selectedColor, setSelectedColor] = useState<ColorOption>(defaultColor);
  const [selectedSize, setSelectedSize] = useState<string>(
    product.sizes?.[0] || 'All Size'
  );
  const [quantity, setQuantity] = useState<number>(1);
  const [activeImage, setActiveImage] = useState<string>(
    product.colors?.[0]?.image ||
      product.gallery?.[0] ||
      'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80'
  );
  const [showSizeChart, setShowSizeChart] = useState<boolean>(false);
  const [reviewFilter, setReviewFilter] = useState<'all' | 'photo' | '5star'>('all');

  // Synchronize state when product changes
  useEffect(() => {
    if (product) {
      const col =
        product.colors && product.colors.length > 0
          ? product.colors[0]
          : {
              name: 'Signature',
              hex: '#0B132B',
              image:
                product.gallery?.[0] ||
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
            };
      setSelectedColor(col);
      setSelectedSize(product.sizes?.[0] || 'All Size');
      setActiveImage(
        col.image ||
          product.gallery?.[0] ||
          'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80'
      );
      setQuantity(1);
    }
  }, [product]);

  // Compute Active Variant and its exact price and stock
  const activeVariant = useMemo(() => {
    const sColor = (selectedColor?.name || '').toLowerCase();
    const sSize = (selectedSize || '').toLowerCase();

    const found = product.variants?.find((v) => {
      const vColor = (v.color?.name || '').toLowerCase();
      const vSize = (v.size || '').toLowerCase();

      const colorMatch = !vColor || !sColor || vColor === sColor;
      const sizeMatch = !vSize || !sSize || vSize === sSize;

      return colorMatch && sizeMatch;
    });

    if (found) return found;

    // Calculate dynamic price based on color + size surcharge
    const colorExtra = selectedColor?.priceExtra || 0;
    const sizeExtra = product.sizePriceExtra?.[selectedSize] || 0;
    const finalPrice = (product.price || 0) + colorExtra + sizeExtra;
    const compareAt = product.originalPrice
      ? product.originalPrice + colorExtra + sizeExtra
      : null;

    const colorName = selectedColor?.name || 'Signature';
    const slugPrefix = (product.slug || 'MLG').substring(0, 4).toUpperCase();
    const colorPrefix = colorName.substring(0, 3).toUpperCase();

    return {
      id: `${product.id}-${colorName}-${selectedSize}`,
      sku: `MLG-${slugPrefix}-${colorPrefix}-${selectedSize}`,
      title: `${product.title} - ${colorName} / ${selectedSize}`,
      color: selectedColor,
      size: selectedSize,
      price: finalPrice,
      compareAtPrice: compareAt,
      availableStock: 10,
      isInStock: true,
    };
  }, [product, selectedColor, selectedSize]);

  const currentPrice = activeVariant.price;
  const currentCompareAt = activeVariant.compareAtPrice || product.originalPrice;
  const isCustomPriced = currentPrice !== product.price;

  const handleColorChange = (color: ColorOption) => {
    setSelectedColor(color);
    setActiveImage(color.image);
  };

  const handleSizeChange = (size: string) => {
    setSelectedSize(size);
  };

  const handleQuantity = (delta: number) => {
    setQuantity((prev) => {
      const next = prev + delta;
      if (
        next >= 1 &&
        next <= (activeVariant.availableStock || product.stockTotal || 99)
      ) {
        return next;
      }
      return prev;
    });
  };

  const handleAddToBag = (e?: React.MouseEvent) => {
    if (e) e.preventDefault();

    let startX = typeof window !== 'undefined' ? window.innerWidth / 2 : 200;
    let startY = typeof window !== 'undefined' ? window.innerHeight / 2 : 200;

    if (e && e.currentTarget) {
      const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
      startX = rect.left + rect.width / 2;
      startY = rect.top + rect.height / 2;
    }

    const flyImg = selectedColor.image || activeImage || product.gallery[0];
    triggerFly(flyImg, startX, startY);

    addToCart({
      productId: product.id,
      variantId: activeVariant.id,
      sku: activeVariant.sku,
      slug: product.slug,
      title: product.title,
      color: selectedColor.name,
      size: selectedSize,
      price: currentPrice,
      originalPrice: currentCompareAt,
      quantity,
      image: selectedColor.image,
    });
  };

  const handleInstantBuy = (e?: React.MouseEvent) => {
    if (e) e.preventDefault();
    instantBuy({
      productId: product.id,
      variantId: activeVariant.id,
      sku: activeVariant.sku,
      slug: product.slug,
      title: product.title,
      color: selectedColor.name,
      size: selectedSize,
      price: currentPrice,
      originalPrice: currentCompareAt,
      quantity,
      image: selectedColor.image,
    });
    router.push('/checkout');
  };

  const reviewsList = useMemo<any[]>(() => [], []);
  const filteredReviews = useMemo<any[]>(() => [], []);

  const relatedProducts = useMemo(() => {
    const pool =
      allProducts && allProducts.length > 0 ? allProducts : productsCatalog;
    return pool
      .filter((p) => p.id !== product.id && p.slug !== product.slug)
      .slice(0, 4);
  }, [allProducts, product]);

  const isCurrentProductFavorited =
    isInWishlist(product.id) || isInWishlist(product.slug);

  const isNumericSizeProduct = useMemo(() => {
    if (product.sizes && product.sizes.some((s) => s && /^\d+$/.test(String(s).trim()))) {
      return true;
    }
    const categoryAndName = `${product.category || ''} ${product.title || ''}`.toLowerCase();
    return ['celana', 'pants', 'jeans', 'denim', 'cargo', 'chino', 'trouser', 'short'].some(
      (k) => categoryAndName.includes(k)
    );
  }, [product.sizes, product.category, product.title]);

  const isAllSizeProduct = useMemo(() => {
    if (
      product.sizes &&
      product.sizes.length === 1 &&
      (product.sizes[0]?.toLowerCase() === 'all size' ||
        product.sizes[0]?.toLowerCase() === 'one size')
    ) {
      return true;
    }
    const categoryAndName = `${product.category || ''} ${product.title || ''}`.toLowerCase();
    return [
      'aksesoris',
      'accessories',
      'cap',
      'topi',
      'bag',
      'tas',
      'belt',
      'ikat pinggang',
      'dompet',
      'wallet',
    ].some((k) => categoryAndName.includes(k));
  }, [product.sizes, product.category, product.title]);

  return {
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
    isCustomPriced,
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
  };
}
