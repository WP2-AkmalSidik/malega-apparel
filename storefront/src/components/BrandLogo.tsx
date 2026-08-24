'use client';

import React from 'react';
import Link from 'next/link';

interface BrandLogoProps {
  className?: string;
  size?: 'sm' | 'md' | 'lg';
}

export default function BrandLogo({ className = '', size = 'md' }: BrandLogoProps) {
  const scaleClasses = {
    sm: 'scale-75 origin-left',
    md: 'scale-90 sm:scale-100',
    lg: 'scale-110 sm:scale-125'
  };

  return (
    <Link href="/" className={`inline-flex items-center gap-3.5 group cursor-pointer ${scaleClasses[size]} ${className}`}>
      {/* Monogram SVG Icon matching official brand logo */}
      <div className="relative shrink-0 transition-transform duration-300 group-hover:scale-105">
        <svg 
          width="48" 
          height="48" 
          viewBox="0 0 100 100" 
          fill="none" 
          xmlns="http://www.w3.org/2000/svg"
          className="w-10 h-10 sm:w-11 sm:h-11"
        >
          {/* Stylized Geometric 'MA' Monogram in #CBAC70 */}
          <path 
            d="M10 10H24V88H10V10Z" 
            fill="#CBAC70" 
          />
          <path 
            d="M24 10L50 56L76 10H90L50 78L24 10Z" 
            fill="#CBAC70" 
          />
          <path 
            d="M54 48L90 88H72L42 54L54 48Z" 
            fill="#CBAC70" 
          />
          <path 
            d="M48 64H86V76H40L48 64Z" 
            fill="#CBAC70" 
          />
        </svg>
      </div>

      {/* Brand Typography */}
      <div className="flex flex-col justify-center select-none">
        {/* MALEGA text in #FDFCFF */}
        <span className="text-xl sm:text-2xl font-black tracking-[0.2em] text-[#FDFCFF] font-sans leading-none uppercase drop-shadow-sm">
          MALEGA
        </span>
        
        {/* APPAREL with gold horizontal divider lines in #CBAC70 */}
        <div className="flex items-center gap-2 mt-1 w-full">
          <div className="h-[1.5px] flex-1 bg-[#CBAC70]"></div>
          <span className="text-[10px] sm:text-[11px] tracking-[0.35em] text-[#CBAC70] font-bold uppercase leading-none">
            APPAREL
          </span>
          <div className="h-[1.5px] flex-1 bg-[#CBAC70]"></div>
        </div>
      </div>
    </Link>
  );
}
