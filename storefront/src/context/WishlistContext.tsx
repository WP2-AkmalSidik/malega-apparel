'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { Product } from '../types';
import { productsCatalog } from '../data/products';

interface WishlistContextType {
  wishlistIds: string[];
  wishlistProducts: Product[];
  wishlistCount: number;
  toggleWishlist: (productId: string) => void;
  isInWishlist: (productId: string) => boolean;
  clearWishlist: () => void;
  isWishlistOpen: boolean;
  setIsWishlistOpen: (open: boolean) => void;
}

const WishlistContext = createContext<WishlistContextType | undefined>(undefined);

export const WishlistProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [wishlistIds, setWishlistIds] = useState<string[]>([]);
  const [isLoaded, setIsLoaded] = useState(false);
  const [isWishlistOpen, setIsWishlistOpen] = useState<boolean>(false);

  // Load from localStorage on mount
  useEffect(() => {
    try {
      const saved = localStorage.getItem('malega_wishlist');
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed)) {
          setWishlistIds(parsed);
        }
      }
    } catch (e) {
      console.error('Error loading wishlist from localStorage:', e);
    } finally {
      setIsLoaded(true);
    }
  }, []);

  // Save to localStorage whenever wishlistIds changes (only after initial load)
  useEffect(() => {
    if (!isLoaded) return;
    try {
      if (wishlistIds.length > 0) {
        localStorage.setItem('malega_wishlist', JSON.stringify(wishlistIds));
      } else {
        localStorage.removeItem('malega_wishlist');
      }
    } catch (e) {
      console.error('Error saving wishlist to localStorage:', e);
    }
  }, [wishlistIds, isLoaded]);

  const toggleWishlist = (productId: string) => {
    setWishlistIds(prev => {
      if (prev.includes(productId)) {
        return prev.filter(id => id !== productId);
      } else {
        return [...prev, productId];
      }
    });
  };

  const isInWishlist = (productId: string) => {
    return wishlistIds.includes(productId);
  };

  const clearWishlist = () => {
    setWishlistIds([]);
  };

  const wishlistProducts = productsCatalog.filter(p => wishlistIds.includes(p.id) || wishlistIds.includes(p.slug));

  return (
    <WishlistContext.Provider
      value={{
        wishlistIds,
        wishlistProducts,
        wishlistCount: wishlistIds.length,
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
