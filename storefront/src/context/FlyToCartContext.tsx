'use client';

import React, { createContext, useContext, useState, useCallback } from 'react';

interface FlyAnimation {
  id: string;
  image: string;
  startX: number;
  startY: number;
}

interface FlyToCartContextType {
  triggerFly: (image: string, startX: number, startY: number) => void;
  activeAnimations: FlyAnimation[];
  removeAnimation: (id: string) => void;
  bagBounce: number; // increments to trigger bounce CSS
}

const FlyToCartContext = createContext<FlyToCartContextType | undefined>(undefined);

export function FlyToCartProvider({ children }: { children: React.ReactNode }) {
  const [activeAnimations, setActiveAnimations] = useState<FlyAnimation[]>([]);
  const [bagBounce, setBagBounce] = useState(0);

  const triggerFly = useCallback((image: string, startX: number, startY: number) => {
    const id = `fly-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`;
    setActiveAnimations(prev => [...prev, { id, image, startX, startY }]);

    // Trigger bag bounce after animation completes (600ms flight + small buffer)
    setTimeout(() => {
      setBagBounce(prev => prev + 1);
    }, 580);

    // Auto-cleanup after animation finishes
    setTimeout(() => {
      setActiveAnimations(prev => prev.filter(a => a.id !== id));
    }, 750);
  }, []);

  const removeAnimation = useCallback((id: string) => {
    setActiveAnimations(prev => prev.filter(a => a.id !== id));
  }, []);

  return (
    <FlyToCartContext.Provider value={{ triggerFly, activeAnimations, removeAnimation, bagBounce }}>
      {children}
    </FlyToCartContext.Provider>
  );
}

export function useFlyToCart() {
  const context = useContext(FlyToCartContext);
  if (!context) {
    throw new Error('useFlyToCart must be used within a FlyToCartProvider');
  }
  return context;
}
