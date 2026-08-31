'use client';

import React from 'react';
import Link from 'next/link';
import BrandLogo from './BrandLogo';

export default function Footer() {
  return (
    <footer className="bg-[#080E20] border-t border-[#CBAC70]/20 text-[#94A3B8] text-xs pt-10 pb-8 mt-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        {/* Links & Brand Description */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8">
          
          <div className="md:col-span-6 space-y-3">
            <BrandLogo size="md" />
            <p className="text-xs text-[#94A3B8] leading-relaxed max-w-md">
              Malega Apparel menghadirkan busana streetwear berkelas dengan standar siluet kontemporer, konstruksi jahitan berkekuatan tinggi, dan estetika warna abadi.
            </p>
            <div className="flex items-center gap-4 text-xs font-semibold text-[#CBAC70]">
              <a href="#" className="hover:text-white transition-colors">Instagram</a>
              <a href="#" className="hover:text-white transition-colors">TikTok</a>
              <a href="#" className="hover:text-white transition-colors">Spotify Playlist</a>
            </div>
          </div>

          <div className="md:col-span-3 space-y-2.5">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">Katalog Koleksi</h4>
            <ul className="space-y-1.5 text-xs">
              <li><Link href="/katalog/heavyweight-boxy-tees-300gsm" className="hover:text-[#CBAC70]">Heavyweight (300GSM)</Link></li>
              <li><Link href="/katalog/french-terry-outerwear-series" className="hover:text-[#CBAC70]">Fleece Hoodies (380GSM)</Link></li>
              <li><Link href="/katalog/tactical-utility-bottoms" className="hover:text-[#CBAC70]">Utility Cargo & Denim</Link></li>
              <li><Link href="/katalog/atelier-luxury-accessories" className="hover:text-[#CBAC70]">Caps & Atelier Bags</Link></li>
            </ul>
          </div>

          <div className="md:col-span-3 space-y-2.5">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">Layanan Pelanggan</h4>
            <ul className="space-y-1.5 text-xs">
              <li><Link href="/track" className="hover:text-[#CBAC70] font-semibold text-[#CBAC70]">🚚 Lacak Pengiriman Live</Link></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Panduan Ukuran (Size Chart)</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Perawatan Bahan & Pencucian</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Pengiriman & Garansi Retur</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Pertanyaan Umum (FAQ)</a></li>
            </ul>
          </div>

        </div>

        {/* Copyright & Badges */}
        <div className="pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-[#94A3B8]">
          <p>© 2026 MALEGA APPAREL. All rights reserved.</p>
          <div className="flex items-center gap-3">
            <span>Bespoke Streetwear Atelier</span>
          </div>
        </div>

      </div>
    </footer>
  );
}
