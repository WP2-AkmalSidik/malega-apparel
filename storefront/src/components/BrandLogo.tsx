'use client';

import React from 'react';
import Link from 'next/link';

interface BrandLogoProps {
  className?: string;
  size?: 'sm' | 'md' | 'lg';
}

export default function BrandLogo({ className = '', size = 'md' }: BrandLogoProps) {
  const heightStyles = {
    sm: 'h-7 sm:h-8',
    md: 'h-8 sm:h-10',
    lg: 'h-11 sm:h-14'
  };

  return (
    <Link 
      href="/" 
      className={`inline-flex items-center group cursor-pointer transition-transform duration-200 active:scale-95 ${className}`}
    >
      <img
        src="/img/malega-brand.png"
        alt="MALEGA APPAREL"
        className={`w-auto ${heightStyles[size]} object-contain drop-shadow-md brightness-105 group-hover:opacity-90 transition-opacity`}
      />
    </Link>
  );
}
