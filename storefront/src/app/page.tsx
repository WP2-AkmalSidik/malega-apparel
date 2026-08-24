'use client';

import React from 'react';
import Link from 'next/link';
import { ArrowRight, Sparkles, Shield, Flame, CheckCircle, Award, Compass } from 'lucide-react';
import { productsCatalog } from '../data/products';
import ProductCard from '../components/ProductCard';

export default function HomePage() {
  const newDrops = productsCatalog.filter(p => p.isNewDrop).slice(0, 4);
  const bestSellers = productsCatalog.filter(p => p.isBestSeller).slice(0, 4);

  const categories = [
    {
      name: 'Heavyweight Tees',
      subtitle: '300GSM Boxy Drop-Shoulder',
      image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80',
      href: '/products?category=T-Shirts',
      count: '4 Artikel'
    },
    {
      name: 'Hoodies & Outerwear',
      subtitle: '380GSM French Terry & Selvedge',
      image: 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80',
      href: '/products?category=Outerwear',
      count: '3 Artikel'
    },
    {
      name: 'Utility Bottoms',
      subtitle: 'Ripstop Cargo & Raw Denim',
      image: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&auto=format&fit=crop&q=80',
      href: '/products?category=Bottoms',
      count: '3 Artikel'
    },
    {
      name: 'Luxury Accessories',
      subtitle: 'Gold Monogram Caps & Bags',
      image: 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=800&auto=format&fit=crop&q=80',
      href: '/products?category=Accessories',
      count: '2 Artikel'
    }
  ];

  return (
    <div className="space-y-24 pb-16">
      
      {/* 1. Hero Section */}
      <section className="relative pt-12 pb-20 sm:pt-20 sm:pb-28 overflow-hidden">
        {/* Ambient Gold Glows */}
        <div className="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[650px] bg-[#CBAC70]/10 blur-[150px] rounded-full pointer-events-none" />
        <div className="absolute top-1/4 right-5 w-[450px] h-[450px] bg-[#162550]/40 blur-[130px] rounded-full pointer-events-none" />

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            {/* Left Column: Headlines & CTAs */}
            <div className="lg:col-span-7 space-y-6 text-left">
              
              {/* Release Pill */}
              <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#111D42] border border-[#CBAC70]/40 text-[#CBAC70] text-xs font-bold tracking-widest uppercase">
                <span className="w-2 h-2 rounded-full bg-[#CBAC70] animate-ping" />
                <span>SS26 CAPSULE DROP : THE OBSIDIAN ESSENTIALS</span>
              </div>

              {/* Main Headline */}
              <h1 className="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight leading-[1.06]">
                Redefining <span className="gold-gradient-text">Streetwear</span> with <span className="gold-gradient-pure">300GSM Cotton.</span>
              </h1>

              {/* Subheadline */}
              <p className="text-base sm:text-lg text-[#94A3B8] max-w-xl font-normal leading-relaxed">
                Koleksi esensial streetwear berkarakter kuat dengan gramasi tebal 300GSM, potongan boxy presisi drop-shoulder, dan detail konstruksi jahitan tingkat tinggi.
              </p>

              {/* Action Buttons */}
              <div className="flex flex-wrap items-center gap-4 pt-3">
                <Link
                  href="/products"
                  className="px-8 py-4 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-xs uppercase tracking-widest hover:opacity-95 transition-all shadow-xl hover:shadow-[#CBAC70]/20 flex items-center gap-2 active:scale-95"
                >
                  <span>Explore Catalog</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>

                <Link
                  href="/products?category=T-Shirts"
                  className="px-7 py-4 rounded-xl bg-[#111D42] hover:bg-[#172654] text-[#FDFCFF] border border-[#CBAC70]/30 font-bold text-xs uppercase tracking-wider transition-all active:scale-95"
                >
                  <span>Heavyweight Tees</span>
                </Link>
              </div>

              {/* 3 Quick Proof Points */}
              <div className="grid grid-cols-3 gap-6 pt-8 border-t border-white/10 w-full max-w-lg">
                <div>
                  <p className="text-2xl sm:text-3xl font-black text-[#CBAC70]">300<span className="text-sm font-bold text-white">GSM</span></p>
                  <p className="text-xs text-[#94A3B8] mt-0.5 font-medium">Heavy Combed Cotton</p>
                </div>
                <div>
                  <p className="text-2xl sm:text-3xl font-black text-[#CBAC70]">100<span className="text-sm font-bold text-white">%</span></p>
                  <p className="text-xs text-[#94A3B8] mt-0.5 font-medium">Custom Cut & Sew</p>
                </div>
                <div>
                  <p className="text-2xl sm:text-3xl font-black text-[#CBAC70]">4.9<span className="text-sm font-bold text-white">★</span></p>
                  <p className="text-xs text-[#94A3B8] mt-0.5 font-medium">1.4k+ Verified Reviews</p>
                </div>
              </div>

            </div>

            {/* Right Column: Hero Visual Product Showcase */}
            <div className="lg:col-span-5 relative">
              <div className="relative mx-auto max-w-md lg:max-w-none">
                <div className="luxury-card rounded-3xl p-6 shadow-2xl relative group overflow-hidden">
                  
                  {/* Visual Image */}
                  <div className="aspect-[4/5] rounded-2xl bg-[#070D1F] overflow-hidden relative">
                    <img
                      src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80"
                      alt="Malega Heavyweight Tee"
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                    />
                    
                    <div className="absolute top-4 left-4 bg-[#CBAC70] text-[#0B132B] text-[10px] font-black tracking-wider uppercase px-3 py-1 rounded shadow">
                      FEATURED PIECE
                    </div>
                  </div>

                  {/* Bottom Info inside Card */}
                  <div className="pt-5 space-y-3">
                    <div className="flex justify-between items-start">
                      <div>
                        <h3 className="font-bold text-base text-[#FDFCFF]">
                          Obsidian Heavyweight Boxy Tee
                        </h3>
                        <p className="text-xs text-[#94A3B8]">Deep Onyx • Drop Shoulder Fit</p>
                      </div>
                      <span className="text-lg font-black text-[#CBAC70]">
                        Rp 229.000
                      </span>
                    </div>

                    <Link
                      href="/products/mlg-001"
                      className="w-full py-3 bg-[#172654] hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] border border-[#CBAC70]/40 rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all shadow"
                    >
                      <span>Lihat Detail Produk</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </Link>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* 2. Shop By Category Grid */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/10 pb-4">
          <div>
            <span className="text-xs font-bold text-[#CBAC70] uppercase tracking-widest block font-mono">Curated Disciplines</span>
            <h2 className="text-2xl sm:text-3xl font-black text-[#FDFCFF] uppercase tracking-wide">
              Shop by Category
            </h2>
          </div>
          <Link href="/products" className="text-xs font-bold text-[#CBAC70] hover:underline flex items-center gap-1">
            <span>View All Collections</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {categories.map((cat, idx) => (
            <Link
              key={idx}
              href={cat.href}
              className="group luxury-card rounded-2xl overflow-hidden relative aspect-[3/4] block hover:border-[#CBAC70]/80 transition-all duration-300 shadow-lg"
            >
              <img
                src={cat.image}
                alt={cat.name}
                className="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700 ease-out"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-[#0B132B] via-[#0B132B]/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity" />

              <div className="absolute inset-0 p-6 flex flex-col justify-end space-y-1">
                <span className="text-[10px] font-mono text-[#CBAC70] font-semibold uppercase tracking-widest">
                  {cat.count}
                </span>
                <h3 className="text-lg font-bold text-[#FDFCFF] group-hover:text-[#CBAC70] transition-colors">
                  {cat.name}
                </h3>
                <p className="text-xs text-[#94A3B8]">{cat.subtitle}</p>
                <div className="pt-2 flex items-center gap-1 text-xs font-bold text-[#CBAC70] opacity-0 group-hover:opacity-100 transition-opacity">
                  <span>Explore Series →</span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* 3. New Drops & Featured Capsule */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/10 pb-4">
          <div>
            <div className="flex items-center gap-2 mb-1">
              <Sparkles className="w-4 h-4 text-[#CBAC70]" />
              <span className="text-xs font-bold text-[#CBAC70] uppercase tracking-widest font-mono">Season 2026</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-black text-[#FDFCFF] uppercase tracking-wide">
              New Drops & Signature Pieces
            </h2>
          </div>
          <Link href="/products" className="text-xs font-bold text-[#CBAC70] hover:underline flex items-center gap-1">
            <span>Explore All ({productsCatalog.length})</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {newDrops.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      </section>

      {/* 4. Craftsmanship Standard Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="luxury-card rounded-3xl p-8 sm:p-12 border border-[#CBAC70]/30 relative overflow-hidden">
          <div className="grid lg:grid-cols-12 gap-10 items-center">
            
            <div className="lg:col-span-7 space-y-5">
              <span className="text-xs font-bold text-[#CBAC70] uppercase tracking-widest font-mono">The Craftsmanship Standard</span>
              <h2 className="text-2xl sm:text-4xl font-black text-[#FDFCFF] leading-tight uppercase">
                Heavyweight Cotton 300GSM <br/>
                <span className="gold-gradient-pure">No Compromise on Fabric Quality.</span>
              </h2>
              <p className="text-xs sm:text-sm text-[#94A3B8] leading-relaxed">
                Di Malega Apparel, kami menolak bahan tipis yang mudah susut. Setiap lembar kaos diproduksi dari 100% benang katun combed berkualitas dengan gramasi 300GSM padat, double rib collar 3.5cm, serta teknik pewarnaan Reactive Dye ramah lingkungan.
              </p>

              <div className="grid grid-cols-2 gap-4 pt-2">
                <div className="p-4 rounded-xl bg-[#0B132B]/80 border border-white/10">
                  <span className="text-xs font-black text-[#CBAC70] block font-mono">01. HEAVY FABRIC</span>
                  <p className="text-xs text-[#94A3B8] mt-1">300GSM Solid & kokoh, tidak terawang saat dikenakan.</p>
                </div>
                <div className="p-4 rounded-xl bg-[#0B132B]/80 border border-white/10">
                  <span className="text-xs font-black text-[#CBAC70] block font-mono">02. DOUBLE COLLAR</span>
                  <p className="text-xs text-[#94A3B8] mt-1">Kerah rib 3.5cm ganda tidak melar pasca pencucian berulang.</p>
                </div>
              </div>
            </div>

            <div className="lg:col-span-5 grid grid-cols-2 gap-4">
              <div className="space-y-4">
                <div className="aspect-square rounded-2xl bg-[#070D1F] p-4 flex flex-col justify-end border border-white/10">
                  <span className="text-xs font-mono text-[#CBAC70]">GRAMASI</span>
                  <span className="text-lg font-black text-white">300 GSM</span>
                </div>
                <div className="aspect-[4/3] rounded-2xl bg-[#070D1F] p-4 flex flex-col justify-end border border-white/10">
                  <span className="text-xs font-mono text-emerald-400">BENANG</span>
                  <span className="text-sm font-bold text-white">100% Combed</span>
                </div>
              </div>

              <div className="space-y-4 pt-6">
                <div className="aspect-[4/3] rounded-2xl bg-[#070D1F] p-4 flex flex-col justify-end border border-white/10">
                  <span className="text-xs font-mono text-cyan-400">CUTTING</span>
                  <span className="text-sm font-bold text-white">Boxy Drop</span>
                </div>
                <div className="aspect-square rounded-2xl bg-[#070D1F] p-4 flex flex-col justify-end border border-white/10">
                  <span className="text-xs font-mono text-[#CBAC70]">ORIGINAL</span>
                  <span className="text-lg font-black text-white">Bespoke</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* 5. Best Sellers Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/10 pb-4">
          <div>
            <div className="flex items-center gap-2 mb-1">
              <Flame className="w-4 h-4 text-[#CBAC70]" />
              <span className="text-xs font-bold text-[#CBAC70] uppercase tracking-widest font-mono">Community Favorites</span>
            </div>
            <h2 className="text-2xl sm:text-3xl font-black text-[#FDFCFF] uppercase tracking-wide">
              Most Wanted Bestsellers
            </h2>
          </div>
          <Link href="/products" className="text-xs font-bold text-[#CBAC70] hover:underline flex items-center gap-1">
            <span>View All Bestsellers</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {bestSellers.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      </section>

      {/* 6. VIP Drop Club / Promo Card */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="rounded-3xl bg-gradient-to-br from-[#111D42] via-[#0B132B] to-[#1C2541] border border-[#CBAC70]/40 p-8 sm:p-12 text-center space-y-5 shadow-2xl relative overflow-hidden">
          <div className="inline-block px-4 py-1.5 rounded-full bg-[#CBAC70]/20 border border-[#CBAC70]/40 text-[#CBAC70] text-xs font-bold font-mono tracking-widest">
            EXCLUSIVE VIP PRIVILEGE
          </div>
          <h2 className="text-3xl sm:text-5xl font-black text-[#FDFCFF] tracking-tight uppercase">
            Join the <span className="gold-gradient-pure">Malega VIP Circle</span>
          </h2>
          <p className="text-xs sm:text-sm text-[#94A3B8] max-w-lg mx-auto leading-relaxed">
            Dapatkan voucher diskon 15% untuk pesanan pertama Anda dengan kode promo <strong className="text-[#CBAC70]">MALEGAVIP15</strong>, serta akses awal ke setiap rilis limited drop.
          </p>

          <div className="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
            <input
              type="email"
              placeholder="Masukkan alamat email Anda..."
              className="w-full bg-[#080E20] border border-[#CBAC70]/40 rounded-xl px-4 py-3 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
            />
            <button className="w-full sm:w-auto px-6 py-3 bg-[#CBAC70] hover:bg-[#E3CD99] text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl whitespace-nowrap shadow-lg">
              Klaim 15% OFF
            </button>
          </div>
        </div>
      </section>

    </div>
  );
}
