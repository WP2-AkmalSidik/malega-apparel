'use client';

import React, { useState, useMemo, Suspense } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { 
  Search, 
  SlidersHorizontal, 
  ArrowLeft,
  ArrowRight,
  Sparkles,
  Layers,
  Flame,
  Check,
  X,
  ArrowUpDown
} from 'lucide-react';
import { productsCatalog } from '../../data/products';
import ProductCard from '../../components/ProductCard';

interface CatalogSeries {
  id: string;
  name: string;
  subtitle: string;
  description: string;
  badge: string;
  specs: string;
  categoryFilter?: string;
  specialFilter?: 'newDrop' | 'bestSeller';
  image: string;
  accentColor: string;
}

const catalogSeriesList: CatalogSeries[] = [
  {
    id: 'heavyweight-tees',
    name: 'Heavyweight Boxy Tees (300GSM)',
    subtitle: 'Siluet Boxy Drop-Shoulder & Kerah Rib 3.5cm',
    description: 'Rilisan kaos esensial streetwear berbahan 100% Cotton Combed 300GSM padat, anti-susut, dan tidak terawang.',
    badge: '300GSM COTTON',
    specs: '4 Varian Warna • S sampai XXL',
    categoryFilter: 'T-Shirts',
    image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
    accentColor: '#CBAC70'
  },
  {
    id: 'hoodies-outerwear',
    name: 'Hoodies & Outerwear Series',
    subtitle: '380GSM Heavyweight French Terry Fleece',
    description: 'Koleksi jaket hoodie & outerwear dengan konstruksi benang katun fleece tebal, double-layered hood, dan finishing mewah.',
    badge: '380GSM FLEECE',
    specs: '3 Varian Warna • S sampai XXL',
    categoryFilter: 'Outerwear',
    image: 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
    accentColor: '#CBAC70'
  },
  {
    id: 'utility-bottoms',
    name: 'Utility Cargo & Selvedge Denim',
    subtitle: 'Ripstop Multi-Pocket & 14oz Raw Denim',
    description: 'Celana cargo fungsional dengan multi-kompartemen saku taktis serta selvedge denim berstruktur kokoh.',
    badge: 'TACTICAL & RAW',
    specs: 'Size 28 sampai 36',
    categoryFilter: 'Bottoms',
    image: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80',
    accentColor: '#CBAC70'
  },
  {
    id: 'luxury-accessories',
    name: 'Atelier Headwear & Bags',
    subtitle: 'Gold Embroidered Caps & Utility Bags',
    description: 'Aksesoris pelengkap busana streetwear dengan monogram geometris Malega berbenang emas dan material Cordura tahan air.',
    badge: 'GOLD EMBROIDERY',
    specs: 'All Size Adjustable',
    categoryFilter: 'Accessories',
    image: 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
    accentColor: '#CBAC70'
  },
  {
    id: 'ss26-drops',
    name: 'SS26 Capsule Drop: Obsidian',
    subtitle: 'Edisi Rilisan Musim Terbatas 2026',
    description: 'Kurasi rilisan busana paling mutakhir dari Atelier Malega dengan palet warna Cosmic Obsidian & Champagne Gold.',
    badge: 'LIMITED CAPSULE',
    specs: 'Limited Production Run',
    specialFilter: 'newDrop',
    image: 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
    accentColor: '#CBAC70'
  },
  {
    id: 'bestseller-archive',
    name: 'Signature Bestsellers Archive',
    subtitle: 'Artikel Terfavorit Pilihan Komunitas',
    description: 'Deretan artikel dengan ulasan bintang 5 tertinggi dan volume penjualan terlaris sejak pertama kali dirilis.',
    badge: 'COMMUNITY CHOICE',
    specs: 'Rating 4.9★ Terverifikasi',
    specialFilter: 'bestSeller',
    image: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=900&auto=format&fit=crop&q=80',
    accentColor: '#CBAC70'
  }
];

function CatalogViewContent() {
  const searchParams = useSearchParams();
  const router = useRouter();
  
  const categoryParam = searchParams.get('category');
  const collectionParam = searchParams.get('collection');
  const searchParam = searchParams.get('search');

  const [activeCategory, setActiveCategory] = useState<string>(categoryParam || '');
  const [activeCollection, setActiveCollection] = useState<string>(collectionParam || '');
  const [searchQuery, setSearchQuery] = useState<string>(searchParam || '');
  const [sortBy, setSortBy] = useState<string>('featured');
  const [isFilterModalOpen, setIsFilterModalOpen] = useState<boolean>(false);
  const [selectedSize, setSelectedSize] = useState<string>('all');

  // Determine if viewing a specific category/collection product list or the catalog directory
  const isViewingSpecificCatalog = Boolean(activeCategory || activeCollection || searchQuery);

  const selectedCatalogInfo = useMemo(() => {
    if (activeCategory) {
      return catalogSeriesList.find(c => c.categoryFilter === activeCategory);
    }
    if (activeCollection) {
      return catalogSeriesList.find(c => c.id === activeCollection || c.specialFilter === activeCollection);
    }
    return null;
  }, [activeCategory, activeCollection]);

  const filteredProducts = useMemo(() => {
    return productsCatalog.filter(p => {
      if (activeCategory && p.category !== activeCategory) return false;
      if (activeCollection === 'ss26-drops' && !p.isNewDrop) return false;
      if (activeCollection === 'bestseller-archive' && !p.isBestSeller) return false;
      if (searchQuery.trim() !== '') {
        const q = searchQuery.toLowerCase();
        const matchTitle = p.title.toLowerCase().includes(q);
        const matchDesc = p.description.toLowerCase().includes(q);
        const matchCat = p.category.toLowerCase().includes(q);
        const matchMaterial = p.material.toLowerCase().includes(q);
        if (!matchTitle && !matchDesc && !matchCat && !matchMaterial) return false;
      }
      if (selectedSize !== 'all' && !p.sizes.includes(selectedSize)) return false;
      return true;
    }).sort((a, b) => {
      if (sortBy === 'price-low') return a.price - b.price;
      if (sortBy === 'price-high') return b.price - a.price;
      if (sortBy === 'sold') return b.soldCount - a.soldCount;
      if (sortBy === 'rating') return b.rating - a.rating;
      return 0;
    });
  }, [activeCategory, activeCollection, searchQuery, selectedSize, sortBy]);

  const handleSelectCatalog = (series: CatalogSeries) => {
    if (series.categoryFilter) {
      setActiveCategory(series.categoryFilter);
      setActiveCollection('');
    } else if (series.id) {
      setActiveCollection(series.id);
      setActiveCategory('');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleBackToAllCatalogs = () => {
    setActiveCategory('');
    setActiveCollection('');
    setSearchQuery('');
    setSelectedSize('all');
    router.push('/products');
  };

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-6">
      
      {/* 1. If NOT viewing specific collection -> Show All Catalog Cards Grid */}
      {!isViewingSpecificCatalog ? (
        <div className="space-y-6">
          
          {/* Header */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
            <div className="space-y-1">
              <div className="flex items-center gap-2">
                <Layers className="w-4 h-4 text-[#CBAC70]" />
                <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-[#CBAC70]">
                  DIREKTORI KATALOG MALEGA
                </span>
              </div>
              <h1 className="text-xl sm:text-2xl font-black text-[#FDFCFF] uppercase tracking-wide">
                Koleksi & Series Tersedia
              </h1>
              <p className="text-xs text-[#94A3B8] max-w-xl">
                Pilih salah satu katalog di bawah untuk menjelajahi artikel produk spesifik sesuai kategori dan siluet pilihan Anda.
              </p>
            </div>

            <Link
              href="/"
              className="self-start sm:self-auto px-4 py-2 rounded-xl bg-[#0E1736] border border-[#CBAC70]/30 hover:border-[#CBAC70] text-[#CBAC70] text-xs font-bold transition-all shadow-sm active:scale-95"
            >
              Lihat Semua List Produk (Home) →
            </Link>
          </div>

          {/* Catalog Cards Grid (Double-Wrapped Luxury Packaging) */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            {catalogSeriesList.map((series) => {
              const productCount = series.categoryFilter
                ? productsCatalog.filter(p => p.category === series.categoryFilter).length
                : series.specialFilter === 'newDrop'
                ? productsCatalog.filter(p => p.isNewDrop).length
                : productsCatalog.filter(p => p.isBestSeller).length;

              return (
                <div
                  key={series.id}
                  onClick={() => handleSelectCatalog(series)}
                  className="group cursor-pointer rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/25 hover:border-[#CBAC70] transition-all duration-300 shadow-xl hover:shadow-[#CBAC70]/15 flex flex-col justify-between"
                >
                  {/* Inner Card Frame */}
                  <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 overflow-hidden flex flex-col justify-between h-full">
                    
                    {/* Visual Image */}
                    <div className="relative aspect-[16/10] bg-[#050914] overflow-hidden">
                      <img
                        src={series.image}
                        alt={series.name}
                        className="w-full h-full object-cover group-hover:scale-106 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-[#070D1F] via-[#070D1F]/30 to-transparent opacity-80 group-hover:opacity-60 transition-opacity" />

                      {/* Badge Top Left */}
                      <div className="absolute top-3 left-3">
                        <span className="bg-[#CBAC70] text-[#0B132B] font-black text-[9px] tracking-widest uppercase px-2.5 py-0.5 rounded shadow">
                          {series.badge}
                        </span>
                      </div>

                      {/* Item Count Bottom Right */}
                      <div className="absolute bottom-3 right-3">
                        <span className="text-[10px] font-bold text-white bg-[#0B132B]/85 border border-white/20 backdrop-blur-md px-2.5 py-1 rounded-lg">
                          {productCount} Artikel Produk
                        </span>
                      </div>
                    </div>

                    {/* Content Info */}
                    <div className="p-4 sm:p-5 flex flex-col justify-between flex-1 space-y-3">
                      <div className="space-y-1">
                        <h3 className="font-black text-sm sm:text-base text-[#FDFCFF] group-hover:text-[#CBAC70] transition-colors leading-snug">
                          {series.name}
                        </h3>
                        <p className="text-xs text-[#CBAC70] font-medium">
                          {series.subtitle}
                        </p>
                        <p className="text-[11px] text-[#94A3B8] line-clamp-2 leading-relaxed pt-1">
                          {series.description}
                        </p>
                      </div>

                      {/* Action CTA */}
                      <div className="pt-3 border-t border-white/10 flex items-center justify-between text-xs font-bold text-[#CBAC70]">
                        <span className="text-[10px] text-[#94A3B8] font-mono">{series.specs}</span>
                        <span className="flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                          Buka Katalog →
                        </span>
                      </div>
                    </div>

                  </div>
                </div>
              );
            })}
          </div>

        </div>
      ) : (
        /* 2. If Viewing Specific Catalog Series -> Show Products in This Collection */
        <div className="space-y-5 animate-in fade-in">
          
          {/* Header with Back Button & Collection Banner */}
          <div className="rounded-2xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-4 sm:p-5 border border-[#CBAC70]/30 shadow-xl space-y-3">
            <button
              onClick={handleBackToAllCatalogs}
              className="inline-flex items-center gap-1.5 text-xs font-bold text-[#CBAC70] hover:text-white transition-colors"
            >
              <ArrowLeft className="w-4 h-4" />
              <span>Kembali ke Semua Katalog</span>
            </button>

            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-white/10 pt-3">
              <div className="space-y-1">
                <span className="text-[10px] font-mono font-bold uppercase tracking-widest text-[#CBAC70] block">
                  KATALOG AKTIF
                </span>
                <h1 className="text-xl sm:text-2xl font-black text-[#FDFCFF] uppercase tracking-wide">
                  {selectedCatalogInfo?.name || activeCategory || activeCollection || searchQuery}
                </h1>
                <p className="text-xs text-[#94A3B8]">
                  {selectedCatalogInfo?.subtitle || `Menampilkan produk artikel dalam kategori ini`}
                </p>
              </div>

              <span className="self-start sm:self-auto text-xs font-mono font-bold text-[#CBAC70] bg-[#070D1F] border border-[#CBAC70]/30 px-3 py-1.5 rounded-xl">
                {filteredProducts.length} Produk Ditemukan
              </span>
            </div>
          </div>

          {/* Search & Filter Bar */}
          <div className="flex items-center gap-2">
            <div className="relative flex-1">
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Cari dalam katalog ini..."
                className="w-full bg-[#0E1736] border border-white/10 hover:border-white/20 focus:border-[#CBAC70] rounded-xl pl-8 sm:pl-9 pr-7 py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none transition-colors shadow-sm"
              />
              <Search className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#CBAC70] absolute left-2.5 sm:left-3 top-2.5" />
              {searchQuery && (
                <button onClick={() => setSearchQuery('')} className="absolute right-2.5 top-2.5 text-xs text-[#94A3B8] hover:text-white">
                  <X className="w-3.5 h-3.5" />
                </button>
              )}
            </div>

            {/* Sort Select */}
            <div className="flex items-center gap-1.5 bg-[#0E1736] border border-white/10 rounded-xl px-2.5 py-1.5 shrink-0">
              <ArrowUpDown className="w-3.5 h-3.5 text-[#CBAC70]" />
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="bg-transparent text-xs text-[#FDFCFF] focus:outline-none cursor-pointer pr-1"
              >
                <option value="featured" className="bg-[#0B132B]">Paling Populer</option>
                <option value="sold" className="bg-[#0B132B]">Terlaris (Sold)</option>
                <option value="price-low" className="bg-[#0B132B]">Harga: Rendah ke Tinggi</option>
                <option value="price-high" className="bg-[#0B132B]">Harga: Tinggi ke Rendah</option>
                <option value="rating" className="bg-[#0B132B]">Rating Tertinggi</option>
              </select>
            </div>
          </div>

          {/* Product Grid */}
          {filteredProducts.length === 0 ? (
            <div className="rounded-2xl bg-[#0E1736] border border-white/10 p-10 text-center space-y-3">
              <p className="text-sm font-bold text-[#FDFCFF]">Tidak ada produk yang sesuai dalam katalog ini</p>
              <button
                onClick={handleBackToAllCatalogs}
                className="px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase"
              >
                Kembali ke Katalog
              </button>
            </div>
          ) : (
            <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-4 lg:gap-5">
              {filteredProducts.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}

        </div>
      )}

    </div>
  );
}

export default function CatalogPage() {
  return (
    <Suspense fallback={<div className="p-8 text-center text-[#CBAC70]">Memuat Katalog Malega...</div>}>
      <CatalogViewContent />
    </Suspense>
  );
}
