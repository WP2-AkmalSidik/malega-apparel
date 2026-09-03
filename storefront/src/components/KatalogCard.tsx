'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowRight, Layers, Sparkles } from 'lucide-react';
import { CatalogCollection } from '../types';

interface KatalogCardProps {
  katalog?: CatalogCollection;
  collection?: CatalogCollection;
}

export default function KatalogCard({ katalog, collection }: KatalogCardProps) {
  const data = collection || katalog;
  if (!data) return null;

  return (
    /* OUTER CARD (First Layer of Packaging) */
    <div className="group relative rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/25 hover:border-[#CBAC70]/70 transition-all duration-300 shadow-xl hover:shadow-[#CBAC70]/15 flex flex-col justify-between">
      
      {/* Corner Accents */}
      <div className="absolute top-1.5 left-1.5 w-2 h-2 border-t border-l border-[#CBAC70]/40 rounded-tl pointer-events-none" />
      <div className="absolute top-1.5 right-1.5 w-2 h-2 border-t border-r border-[#CBAC70]/40 rounded-tr pointer-events-none" />
      <div className="absolute bottom-1.5 left-1.5 w-2 h-2 border-b border-l border-[#CBAC70]/40 rounded-bl pointer-events-none" />
      <div className="absolute bottom-1.5 right-1.5 w-2 h-2 border-b border-r border-[#CBAC70]/40 rounded-br pointer-events-none" />

      {/* INNER CARD (Second Layer of Packaging) */}
      <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 overflow-hidden flex flex-col justify-between h-full">
        
        {/* Visual Cover Image */}
        <Link href={`/katalog/${data.slug}`} className="relative aspect-[16/10] bg-[#050914] overflow-hidden block cursor-pointer">
          <img
            src={data.coverImage}
            alt={data.title || data.name}
            className="w-full h-full object-cover group-hover:scale-106 transition-transform duration-500 ease-out"
          />

          {/* Gradient Overlay */}
          <div className="absolute inset-0 bg-gradient-to-t from-[#070D1F] via-[#070D1F]/30 to-transparent opacity-80 group-hover:opacity-60 transition-opacity" />

          {/* Top Badge */}
          {data.badge && (
            <div className="absolute top-2.5 left-2.5 z-10">
              <span className="bg-[#CBAC70] text-[#0B132B] text-[9px] sm:text-[10px] font-black tracking-widest uppercase px-2 sm:px-2.5 py-0.5 rounded shadow">
                {data.badge}
              </span>
            </div>
          )}

          {/* Season & Article Count Bottom Left & Right */}
          <div className="absolute bottom-2.5 left-2.5 right-2.5 flex items-center justify-between z-10 text-[10px] text-white">
            <span className="font-mono text-[#CBAC70] bg-[#0B132B]/90 border border-[#CBAC70]/30 px-2 py-0.5 rounded backdrop-blur-md font-bold">
              {data.season} • {data.releaseYear}
            </span>
            <span className="bg-[#0B132B]/90 border border-white/20 px-2 py-0.5 rounded backdrop-blur-md font-bold">
              {data.totalArticles} Artikel
            </span>
          </div>
        </Link>

        {/* Content Details */}
        <div className="p-3.5 sm:p-5 flex flex-col justify-between flex-1 space-y-3">
          
          <div className="space-y-1.5">
            
            {/* Color Palette Preview */}
            <div className="flex items-center gap-1.5">
              {data.palette && data.palette.map((colorHex, idx) => (
                <span
                  key={idx}
                  className="w-3 h-3 rounded-full border border-white/20 shadow-sm"
                  style={{ backgroundColor: colorHex }}
                />
              ))}
              {data.featuredMaterial && (
                <span className="text-[10px] text-[#94A3B8] ml-1 font-mono">{data.featuredMaterial}</span>
              )}
            </div>

            {/* Title & Subtitle */}
            <Link href={`/katalog/${data.slug}`} className="block">
              <h3 className="text-sm sm:text-base font-black text-[#FDFCFF] group-hover:text-[#CBAC70] transition-colors leading-snug">
                {data.title || data.name}
              </h3>
              {data.subtitle && (
                <p className="text-xs text-[#CBAC70] font-semibold mt-0.5 line-clamp-1">
                  {data.subtitle}
                </p>
              )}
            </Link>

            <p className="text-[11px] text-[#94A3B8] line-clamp-2 leading-relaxed">
              {data.description}
            </p>

            {/* Tags */}
            {data.tags && data.tags.length > 0 && (
              <div className="flex flex-wrap gap-1 pt-1">
                {data.tags.map((tag, tIdx) => (
                  <span key={tIdx} className="bg-[#14204A] text-[#94A3B8] border border-white/5 text-[9px] font-semibold px-2 py-0.2 rounded-md">
                    #{tag}
                  </span>
                ))}
              </div>
            )}

          </div>

          {/* Action CTA */}
          <div className="pt-3 border-t border-white/10 flex items-center justify-between">
            <span className="text-[10px] font-mono text-[#94A3B8]">Atelier Malega</span>
            <Link
              href={`/katalog/${data.slug}`}
              className="inline-flex items-center gap-1 text-xs font-bold text-[#CBAC70] group-hover:text-[#FDFCFF] transition-colors"
            >
              <span>Jelajahi Katalog Seri</span>
              <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
            </Link>
          </div>

        </div>

      </div>

    </div>
  );
}
