'use client';

import React, { useEffect, useState, useRef } from 'react';
import { createPortal } from 'react-dom';
import { useFlyToCart } from '../context/FlyToCartContext';

export default function FlyToCartAnimation() {
  const { activeAnimations, removeAnimation } = useFlyToCart();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  if (!mounted) return null;

  return createPortal(
    <>
      {activeAnimations.map((anim) => (
        <FlyingItem
          key={anim.id}
          id={anim.id}
          image={anim.image}
          startX={anim.startX}
          startY={anim.startY}
          onComplete={() => removeAnimation(anim.id)}
        />
      ))}
    </>,
    document.body
  );
}

function FlyingItem({
  id,
  image,
  startX,
  startY,
  onComplete,
}: {
  id: string;
  image: string;
  startX: number;
  startY: number;
  onComplete: () => void;
}) {
  const [style, setStyle] = useState<React.CSSProperties>({
    position: 'fixed',
    left: `${startX - 28}px`,
    top: `${startY - 28}px`,
    opacity: 0,
    pointerEvents: 'none',
  });

  useEffect(() => {
    // Find the bag button as the target (desktop or mobile)
    const bagEl = document.getElementById('navbar-bag-button');
    if (!bagEl) {
      onComplete();
      return;
    }

    const bagRect = bagEl.getBoundingClientRect();
    const endX = bagRect.left + bagRect.width / 2;
    const endY = bagRect.top + bagRect.height / 2;

    const deltaX = endX - startX;
    const deltaY = endY - startY;

    setStyle({
      position: 'fixed',
      left: `${startX - 28}px`,
      top: `${startY - 28}px`,
      zIndex: 99999,
      pointerEvents: 'none',
      ['--fly-dx' as string]: `${deltaX}px`,
      ['--fly-dy' as string]: `${deltaY}px`,
    });

    const timer = setTimeout(onComplete, 700);
    return () => clearTimeout(timer);
  }, [startX, startY, onComplete]);

  return (
    <div style={style} className="fly-to-cart-item">
      {/* Outer wrapper: handles the arc and gold glow */}
      <div className="fly-to-cart-inner relative">
        <div className="w-14 h-14 rounded-2xl overflow-hidden border-2 border-[#CBAC70] shadow-[0_0_20px_rgba(203,172,112,0.8)] bg-[#0B132B]">
          <img
            src={image}
            alt="Flying product"
            className="w-full h-full object-cover"
            draggable={false}
          />
          {/* Shimmer gradient overlay */}
          <div className="absolute inset-0 bg-gradient-to-tr from-[#CBAC70]/30 via-transparent to-[#FDFCFF]/40 pointer-events-none" />
        </div>
        {/* Trailing golden star particle */}
        <div className="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-[#E3CD99] blur-xs animate-ping" />
      </div>
    </div>
  );
}
