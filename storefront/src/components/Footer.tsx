'use client';

import React from 'react';
import Link from 'next/link';
import BrandLogo from './BrandLogo';
import { ExternalLink } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="bg-[#080E20] border-t border-[#CBAC70]/20 text-[#94A3B8] text-xs pt-10 pb-8 mt-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        {/* Links & Brand Description */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8">
          
          <div className="md:col-span-5 space-y-3">
            <BrandLogo size="md" />
            <p className="text-xs text-[#94A3B8] leading-relaxed max-w-sm">
              Malega Apparel menghadirkan busana streetwear berkelas dengan standar siluet kontemporer, konstruksi jahitan berkekuatan tinggi, dan estetika warna abadi.
            </p>
            <div className="flex items-center gap-4 text-xs font-semibold text-[#CBAC70]">
              <a href="#" className="hover:text-white transition-colors">Instagram</a>
              <a href="#" className="hover:text-white transition-colors">TikTok</a>
              <a href="#" className="hover:text-white transition-colors">Spotify Playlist</a>
            </div>
          </div>

          <div className="md:col-span-2 space-y-2.5">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">Koleksi</h4>
            <ul className="space-y-1.5 text-xs">
              <li><Link href="/products?category=T-Shirts" className="hover:text-[#CBAC70]">Heavyweight Tees</Link></li>
              <li><Link href="/products?category=Outerwear" className="hover:text-[#CBAC70]">Fleece Hoodies</Link></li>
              <li><Link href="/products?category=Bottoms" className="hover:text-[#CBAC70]">Utility Cargo</Link></li>
              <li><Link href="/products?category=Accessories" className="hover:text-[#CBAC70]">Caps & Bags</Link></li>
            </ul>
          </div>

          <div className="md:col-span-2 space-y-2.5">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">Layanan Pelanggan</h4>
            <ul className="space-y-1.5 text-xs">
              <li><a href="#" className="hover:text-[#CBAC70]">Panduan Ukuran</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Perawatan Bahan</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Pengiriman & Retur</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">FAQ</a></li>
            </ul>
          </div>

          <div className="md:col-span-3 space-y-2.5">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">Portal Manajemen</h4>
            <p className="text-xs text-[#94A3B8]">Akses portal internal tim manajemen toko.</p>
            <a 
              href="http://localhost:8000" 
              target="_blank" 
              rel="noopener noreferrer"
              className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#111D42] border border-[#CBAC70]/30 hover:border-[#CBAC70] text-xs font-bold text-[#FDFCFF] transition-colors shadow"
            >
              <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
              <span>Backoffice Portal (Laravel)</span>
              <ExternalLink className="w-3.5 h-3.5 text-[#CBAC70]" />
            </a>
          </div>

        </div>

        {/* Copyright & Badges */}
        <div className="pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-[#94A3B8]">
          <p>© 2026 MALEGA APPAREL. All rights reserved.</p>
          <div className="flex items-center gap-3">
            <span>Enterprise E-Commerce Storefront</span>
          </div>
        </div>

      </div>
    </footer>
  );
}
