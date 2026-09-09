'use client';

import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import { Product } from '../types';

interface WishlistContextType {
  wishlistIds: string[];
  wishlistProducts: Product[];
  wishlistCount: number;
  toggleWishlist: (productId: string, product?: Product) => void;
  isInWishlist: (productId: string) => boolean;
  clearWishlist: () => void;
  isWishlistOpen: boolean;
  setIsWishlistOpen: (open: boolean) => void;
}

interface StoredWishlistItem {
  id: string;
  slug: string;
  title: string;
  subtitle: string;
  price: number;
  originalPrice: number;
  discountPercentage: number;
  category: string;
  material: string;
  gsm: number;
  colors: { name: string; hex: string; image: string; priceExtra?: number }[];
  sizes: string[];
  gallery: string[];
  rating: number;
  reviewCount: number;
}

const WishlistContext = createContext<WishlistContextType | undefined>(undefined);

export const WishlistProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [wishlistItems, setWishlistItems] = useState<StoredWishlistItem[]>([]);
  const [isLoaded, setIsLoaded] = useState(false);
  const [isWishlistOpen, setIsWishlistOpen] = useState<boolean>(false);

  // Load from localStorage on mount
  useEffect(() => {
    try {
      const saved = localStorage.getItem('malega_wishlist_v2');
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed)) {
          setWishlistItems(parsed);
        }
      } else {
        // Migrate from old format (array of IDs only) — clear it since we can't resolve those
        const oldSaved = localStorage.getItem('malega_wishlist');
        if (oldSaved) {
          localStorage.removeItem('malega_wishlist');
        }
      }
    } catch (e) {
      console.error('Error loading wishlist from localStorage:', e);
    } finally {
      setIsLoaded(true);
    }
  }, []);

  // Save to localStorage whenever wishlistItems changes (only after initial load)
  useEffect(() => {
    if (!isLoaded) return;
    try {
      if (wishlistItems.length > 0) {
        localStorage.setItem('malega_wishlist_v2', JSON.stringify(wishlistItems));
      } else {
        localStorage.removeItem('malega_wishlist_v2');
      }
    } catch (e) {
      console.error('Error saving wishlist to localStorage:', e);
    }
  }, [wishlistItems, isLoaded]);

  const toggleWishlist = useCallback((productId: string, product?: Product) => {
    setWishlistItems(prev => {
      const exists = prev.some(item => item.id === productId || item.slug === productId);
      if (exists) {
        return prev.filter(item => item.id !== productId && item.slug !== productId);
      } else if (product) {
        const newItem: StoredWishlistItem = {
          id: product.id,
          slug: product.slug,
          title: product.title,
          subtitle: product.subtitle || '',
          price: product.price,
          originalPrice: product.originalPrice,
          discountPercentage: product.discountPercentage,
          category: product.category || 'Streetwear',
          material: product.material || '',
          gsm: product.gsm || 0,
          colors: (product.colors || []).map(c => ({
            name: c.name,
            hex: c.hex,
            image: c.image,
            priceExtra: c.priceExtra,
          })),
          sizes: product.sizes || [],
          gallery: product.gallery || [],
          rating: product.rating || 0,
          reviewCount: product.reviewCount || 0,
        };
        return [...prev, newItem];
      }
      // If no product data provided for adding, just store minimal info
      return prev;
    });
  }, []);

  const isInWishlist = useCallback((productId: string) => {
    return wishlistItems.some(item => item.id === productId || item.slug === productId);
  }, [wishlistItems]);

  const clearWishlist = useCallback(() => {
    setWishlistItems([]);
  }, []);

  // Convert stored items to Product-compatible objects
  const wishlistProducts: Product[] = wishlistItems.map(item => ({
    id: item.id,
    slug: item.slug,
    title: item.title,
    subtitle: item.subtitle,
    price: item.price,
    originalPrice: item.originalPrice,
    discountPercentage: item.discountPercentage || 0,
    category: item.category || 'Streetwear',
    material: item.material || '',
    gsm: item.gsm || 0,
    fit: '',
    origin: 'Bandung, Indonesia',
    stockTotal: 0,
    colors: item.colors || [],
    sizes: item.sizes || [],
    gallery: item.gallery || [],
    features: [],
    specifications: {},
    rating: item.rating || 0,
    reviewCount: item.reviewCount || 0,
    soldCount: 0,
    description: '',
    badge: undefined,
    isNewDrop: false,
    isBestSeller: false,
  }));

  const wishlistIds = wishlistItems.map(item => item.id);

  return (
    <WishlistContext.Provider
      value={{
        wishlistIds,
        wishlistProducts,
        wishlistCount: wishlistItems.length,
        toggleWishlist,
        isInWishlist,
        clearWishlist,
        isWishlistOpen,
        setIsWishlistOpen
      }}
    >
      {children}
    </WishlistContext.Provider>
  );
};

export const useWishlist = () => {
  const context = useContext(WishlistContext);
  if (!context) {
    throw new Error('useWishlist must be used within a WishlistProvider');
  }
  return context;
};
