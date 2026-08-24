'use client';

import React from 'react';
import Link from 'next/link';
import BrandLogo from './BrandLogo';
import { ShieldCheck, Truck, Clock, RefreshCw, ExternalLink } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="bg-[#080E20] border-t border-[#CBAC70]/20 text-[#94A3B8] text-xs pt-16 pb-12 mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        {/* Top 4 Brand Pillars */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pb-12 border-b border-white/10">
          <div className="flex items-center gap-3.5 p-4 rounded-xl bg-[#111D42]/60 border border-[#CBAC70]/20">
            <div className="w-10 h-10 rounded-xl bg-[#CBAC70]/10 border border-[#CBAC70]/30 text-[#CBAC70] flex items-center justify-center shrink-0">
              <ShieldCheck className="w-5 h-5" />
            </div>
            <div>
              <h5 className="font-bold text-[#FDFCFF] text-xs">100% Bespoke Craft</h5>
              <p className="text-[11px] text-[#94A3B8]">Material murni 300GSM</p>
            </div>
          </div>

          <div className="flex items-center gap-3.5 p-4 rounded-xl bg-[#111D42]/60 border border-[#CBAC70]/20">
            <div className="w-10 h-10 rounded-xl bg-[#CBAC70]/10 border border-[#CBAC70]/30 text-[#CBAC70] flex items-center justify-center shrink-0">
              <Truck className="w-5 h-5" />
            </div>
            <div>
              <h5 className="font-bold text-[#FDFCFF] text-xs">Express Delivery</h5>
              <p className="text-[11px] text-[#94A3B8]">Gratis Ongkir XTRA Seluruh ID</p>
            </div>
          </div>

          <div className="flex items-center gap-3.5 p-4 rounded-xl bg-[#111D42]/60 border border-[#CBAC70]/20">
            <div className="w-10 h-10 rounded-xl bg-[#CBAC70]/10 border border-[#CBAC70]/30 text-[#CBAC70] flex items-center justify-center shrink-0">
              <RefreshCw className="w-5 h-5" />
            </div>
            <div>
              <h5 className="font-bold text-[#FDFCFF] text-xs">7-Day Guarantee</h5>
              <p className="text-[11px] text-[#94A3B8]">Garansi tukar size mudah</p>
            </div>
          </div>

          <div className="flex items-center gap-3.5 p-4 rounded-xl bg-[#111D42]/60 border border-[#CBAC70]/20">
            <div className="w-10 h-10 rounded-xl bg-[#CBAC70]/10 border border-[#CBAC70]/30 text-[#CBAC70] flex items-center justify-center shrink-0">
              <Clock className="w-5 h-5" />
            </div>
            <div>
              <h5 className="font-bold text-[#FDFCFF] text-xs">Bespoke Support</h5>
              <p className="text-[11px] text-[#94A3B8]">Respon cepat 24/7</p>
            </div>
          </div>
        </div>

        {/* Links & Brand Description */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-10">
          
          <div className="md:col-span-5 space-y-4">
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

          <div className="md:col-span-2 space-y-3">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">Collections</h4>
            <ul className="space-y-2 text-xs">
              <li><Link href="/products?category=T-Shirts" className="hover:text-[#CBAC70]">Heavyweight Tees</Link></li>
              <li><Link href="/products?category=Outerwear" className="hover:text-[#CBAC70]">Fleece Hoodies</Link></li>
              <li><Link href="/products?category=Bottoms" className="hover:text-[#CBAC70]">Utility Cargo Pants</Link></li>
              <li><Link href="/products?category=Accessories" className="hover:text-[#CBAC70]">Gold Monogram Cap</Link></li>
            </ul>
          </div>

          <div className="md:col-span-2 space-y-3">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">Client Care</h4>
            <ul className="space-y-2 text-xs">
              <li><a href="#" className="hover:text-[#CBAC70]">Size Guide Manual</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Fabric Care & Washing</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">Shipping & Returns</a></li>
              <li><a href="#" className="hover:text-[#CBAC70]">FAQ</a></li>
            </ul>
          </div>

          <div className="md:col-span-3 space-y-3">
            <h4 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70]">System Gateways</h4>
            <p className="text-xs text-[#94A3B8]">Akses portal internal tim manajemen toko.</p>
            <a 
              href="http://localhost:8000" 
              target="_blank" 
              rel="noopener noreferrer"
              className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#111D42] border border-[#CBAC70]/30 hover:border-[#CBAC70] text-xs font-bold text-[#FDFCFF] transition-colors shadow"
            >
              <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
              <span>Backoffice Portal (Laravel)</span>
              <ExternalLink className="w-3.5 h-3.5 text-[#CBAC70]" />
            </a>
          </div>

        </div>

        {/* Copyright & Badges */}
        <div className="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-[#94A3B8]">
          <p>© 2026 MALEGA APPAREL. All rights reserved.</p>
          <div className="flex items-center gap-3">
            <span>Powered by Next.js Enterprise E-Commerce</span>
          </div>
        </div>

      </div>
    </footer>
  );
}
