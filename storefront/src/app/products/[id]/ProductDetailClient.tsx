'use client';

import React, { useState, useEffect, useMemo } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { 
  Star, 
  ShoppingBag, 
  ShieldCheck, 
  Truck, 
  Ruler, 
  ArrowRight, 
  ChevronRight, 
  Sparkles, 
  Heart,
  Zap,
  CheckCircle,
  ThumbsUp,
  Image as ImageIcon,
  Check,
  Tag
} from 'lucide-react';
import { productsCatalog } from '../../../data/products';
import { useCart } from '../../../context/CartContext';
import { useWishlist } from '../../../context/WishlistContext';
import { useFlyToCart } from '../../../context/FlyToCartContext';
import { ColorOption, Product } from '../../../types';
import ProductCard from '../../../components/ProductCard';

interface ProductDetailClientProps {
  productId: string;
  initialProduct?: Product | null;
  allProducts?: Product[];
}

export default function ProductDetailClient({ productId, initialProduct, allProducts }: ProductDetailClientProps) {
  const router = useRouter();
  const { addToCart, instantBuy, setIsCartOpen } = useCart();
  const { isInWishlist, toggleWishlist } = useWishlist();
  const { triggerFly } = useFlyToCart();

  const product = useMemo(() => {
    if (initialProduct) return initialProduct;
    const fromAll = (allProducts || []).find(p => p.id === productId || p.slug === productId);
    if (fromAll) return fromAll;
    return productsCatalog.find(p => p.id === productId || p.slug === productId) || productsCatalog[0];
  }, [initialProduct, allProducts, productId]);

  const defaultColor: ColorOption = (product.colors && product.colors.length > 0) 
    ? product.colors[0] 
    : { name: 'Signature', hex: '#0B132B', image: product.gallery?.[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80' };

  const [selectedColor, setSelectedColor] = useState<ColorOption>(defaultColor);
  const [selectedSize, setSelectedSize] = useState<string>(product.sizes?.[0] || 'All Size');
  const [quantity, setQuantity] = useState<number>(1);
  const [activeImage, setActiveImage] = useState<string>(product.colors?.[0]?.image || product.gallery?.[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80');
  const [showSizeChart, setShowSizeChart] = useState<boolean>(false);
  const [reviewFilter, setReviewFilter] = useState<'all' | 'photo' | '5star'>('all');

  // Synchronize state when product changes
  useEffect(() => {
    if (product) {
      const col = (product.colors && product.colors.length > 0) 
        ? product.colors[0] 
        : { name: 'Signature', hex: '#0B132B', image: product.gallery?.[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80' };
      setSelectedColor(col);
      setSelectedSize(product.sizes?.[0] || 'All Size');
      setActiveImage(col.image || product.gallery?.[0] || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80');
      setQuantity(1);
    }
  }, [product]);

  // Compute Active Variant and its exact price and stock
  const activeVariant = useMemo(() => {
    const found = product.variants?.find(
      v => v.color.name.toLowerCase() === selectedColor.name.toLowerCase() && v.size === selectedSize
    );

    if (found) return found;

    // Calculate dynamic price based on color + size surcharge
    const colorExtra = selectedColor.priceExtra || 0;
    const sizeExtra = product.sizePriceExtra?.[selectedSize] || 0;
    const finalPrice = product.price + colorExtra + sizeExtra;
    const compareAt = product.originalPrice ? product.originalPrice + colorExtra + sizeExtra : null;

    return {
      id: `${product.id}-${selectedColor.name}-${selectedSize}`,
      sku: `MLG-${product.slug.substring(0, 4).toUpperCase()}-${selectedColor.name.substring(0, 3).toUpperCase()}-${selectedSize}`,
      title: `${product.title} - ${selectedColor.name} / ${selectedSize}`,
      color: selectedColor,
      size: selectedSize,
      price: finalPrice,
      compareAtPrice: compareAt,
      availableStock: 10,
      isInStock: true
    };
  }, [product, selectedColor, selectedSize]);

  const currentPrice = activeVariant.price;
  const currentCompareAt = activeVariant.compareAtPrice || product.originalPrice;
  const isCustomPriced = currentPrice !== product.price;

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const handleColorChange = (color: ColorOption) => {
    setSelectedColor(color);
    setActiveImage(color.image);
  };

  const handleSizeChange = (size: string) => {
    setSelectedSize(size);
  };

  const handleQuantity = (delta: number) => {
    setQuantity(prev => {
      const next = prev + delta;
      if (next >= 1 && next <= (activeVariant.availableStock || product.stockTotal || 99)) {
        return next;
      }
      return prev;
    });
  };

  const handleAddToBag = (e?: React.MouseEvent) => {
    if (e) e.preventDefault();

    // Calculate starting position from click event or clicked button
    let startX = typeof window !== 'undefined' ? window.innerWidth / 2 : 200;
    let startY = typeof window !== 'undefined' ? window.innerHeight / 2 : 200;

    if (e && e.currentTarget) {
      const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
      startX = rect.left + rect.width / 2;
      startY = rect.top + rect.height / 2;
    }

    const flyImg = selectedColor.image || activeImage || product.gallery[0];
    triggerFly(flyImg, startX, startY);

    addToCart({
      productId: product.id,
      variantId: activeVariant.id,
      sku: activeVariant.sku,
      slug: product.slug,
      title: product.title,
      color: selectedColor.name,
      size: selectedSize,
      price: currentPrice,
      originalPrice: currentCompareAt,
      quantity,
      image: selectedColor.image
    });
  };

  const handleInstantBuy = (e?: React.MouseEvent) => {
    if (e) e.preventDefault();
    instantBuy({
      productId: product.id,
      variantId: activeVariant.id,
      sku: activeVariant.sku,
      slug: product.slug,
      title: product.title,
      color: selectedColor.name,
      size: selectedSize,
      price: currentPrice,
      originalPrice: currentCompareAt,
      quantity,
      image: selectedColor.image
    });
    router.push('/checkout');
  };

  const reviewsList = [
    {
      id: 'rev-1',
      author: 'Dimas Rizky P.',
      avatar: 'D',
      rating: 5,
      date: '2 hari yang lalu',
      variant: `${selectedColor.name} • Size ${selectedSize}`,
      bodyProfile: 'TB 175cm / BB 72kg (Pas & Boxy Proporsional)',
      comment: 'Beneran tebel 300GSM padat tapi adem dipakai seharian. Jahitan kerah rib-nya kokoh banget gak gampang melar abis dicuci. Potongan bahu drop shoulder-nya beneran pas streetwear look.',
      photos: [
        'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=500&auto=format&fit=crop&q=80'
      ],
      helpful: 46
    },
    {
      id: 'rev-2',
      author: 'Alif Fadhillah',
      avatar: 'A',
      rating: 5,
      date: '5 hari yang lalu',
      variant: 'Midnight Black • Size XL',
      bodyProfile: 'TB 180cm / BB 80kg',
      comment: 'Warna deep black-nya pekat dan mewah banget. Kerah rib 3.5cm rapi gak kopong. Ini brand lokal kualitasnya beneran enterprise standar internasional.',
      photos: [
        'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=500&auto=format&fit=crop&q=80'
      ],
      helpful: 28
    },
    {
      id: 'rev-3',
      author: 'Reza Kurniawan',
      avatar: 'R',
      rating: 5,
      date: '1 minggu yang lalu',
      variant: 'Onyx Black • Size M',
      bodyProfile: 'TB 168cm / BB 62kg',
      comment: 'Packing double boxy sangat aman, pengiriman SPX cepet 1 hari sampai. Kain berat dan gak nerawang sama sekali. Pasti repeat order warna lain!',
      photos: [],
      helpful: 19
    },
    {
      id: 'rev-4',
      author: 'Devina Putri',
      avatar: 'D',
      rating: 5,
      date: '2 minggu yang lalu',
      variant: 'Sand Khaki • Size S',
      bodyProfile: 'TB 160cm / BB 49kg (Oversized Clean Look)',
      comment: 'Buat cewek look-nya jadi oversized keren banget. Bahannya premium tebal jatuh, jatuhnya di badan estetik.',
      photos: [],
      helpful: 12
    }
  ];

  const filteredReviews = reviewsList.filter(r => {
    if (reviewFilter === 'photo') return r.photos.length > 0;
    if (reviewFilter === '5star') return r.rating === 5;
    return true;
  });

  const relatedProducts = useMemo(() => {
    const pool = (allProducts && allProducts.length > 0) ? allProducts : productsCatalog;
    return pool.filter(p => p.id !== product.id && p.slug !== product.slug).slice(0, 4);
  }, [allProducts, product]);
  const isCurrentProductFavorited = isInWishlist(product.id) || isInWishlist(product.slug);

  const isNumericSizeProduct = useMemo(() => {
    if (product.sizes && product.sizes.some(s => /^\d+$/.test(s.trim()))) {
      return true;
    }
    const categoryAndName = (product.category + ' ' + product.title).toLowerCase();
    return ['celana', 'pants', 'jeans', 'denim', 'cargo', 'chino', 'trouser', 'short'].some(k => categoryAndName.includes(k));
  }, [product.sizes, product.category, product.title]);

  const isAllSizeProduct = useMemo(() => {
    if (product.sizes && product.sizes.length === 1 && (product.sizes[0].toLowerCase() === 'all size' || product.sizes[0].toLowerCase() === 'one size')) {
      return true;
    }
    const categoryAndName = (product.category + ' ' + product.title).toLowerCase();
    return ['aksesoris', 'accessories', 'cap', 'topi', 'bag', 'tas', 'belt', 'ikat pinggang', 'dompet', 'wallet'].some(k => categoryAndName.includes(k));
  }, [product.sizes, product.category, product.title]);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8 sm:space-y-12">
      
      {/* Breadcrumb Navigation */}
      <nav className="flex items-center gap-2 text-xs font-mono text-[#94A3B8]">
        <Link href="/" className="hover:text-[#CBAC70] transition-colors">HOME</Link>
        <ChevronRight className="w-3.5 h-3.5" />
        <span className="text-[#CBAC70] uppercase font-bold truncate max-w-xs">{product.title}</span>
      </nav>

      {/* Main Product Showcase Grid (Double-Framed Packaging) */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        
        {/* LEFT COLUMN: Gallery & Lookbook Pictures (6 cols) */}
        <div className="lg:col-span-6 space-y-4">
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/30 shadow-2xl relative">
            
            {/* Primary Visual Picture */}
            <div className="relative aspect-[4/5] rounded-xl sm:rounded-2xl overflow-hidden bg-[#050914] border border-white/10">
              <img
                src={activeImage}
                alt={product.title}
                className="w-full h-full object-cover transition-all duration-300"
              />

              {/* Badges Overlay */}
              <div className="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                <span className="bg-[#CBAC70] text-[#0B132B] text-[9px] sm:text-[10px] font-black tracking-widest uppercase px-2.5 py-1 rounded-md shadow-lg">
                  {product.badge || 'SS26 DROP'}
                </span>
                {product.discountPercentage > 0 && (
                  <span className="bg-[#0B132B]/90 border border-[#CBAC70]/60 text-[#CBAC70] text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded shadow">
                    -{product.discountPercentage}% OFF
                  </span>
                )}
              </div>

              {/* Wishlist Heart Button on Image */}
              <div className="absolute top-3 right-3 z-10 flex items-center gap-2">
                {isCurrentProductFavorited && (
                  <span className="px-2 py-1 rounded-lg bg-rose-500/20 border border-rose-500/40 text-rose-300 text-[10px] font-mono font-bold backdrop-blur-md">
                    ♥ FAVORIT ANDA
                  </span>
                )}
                <button
                  type="button"
                  onClick={() => toggleWishlist(product.id)}
                  className={`p-2.5 rounded-full backdrop-blur-md transition-all cursor-pointer shadow-lg ${
                    isCurrentProductFavorited
                      ? 'bg-rose-500 text-white scale-110'
                      : 'bg-black/60 text-white/80 hover:text-rose-400 hover:bg-black/80'
                  }`}
                  title="Simpan ke Wishlist (Cache)"
                >
                  <Heart className={`w-4 h-4 ${isCurrentProductFavorited ? 'fill-white' : ''}`} />
                </button>
              </div>

              {/* Active Color Info Tag */}
              <div className="absolute bottom-3 left-3 bg-[#080E20]/90 border border-[#CBAC70]/30 backdrop-blur-md px-3 py-1 rounded-lg text-xs font-mono text-[#CBAC70] flex items-center gap-2">
                <span className="w-2.5 h-2.5 rounded-full" style={{ backgroundColor: selectedColor.hex }} />
                <span>Warna: {selectedColor.name}</span>
                <span className="text-slate-400">• SKU: {activeVariant.sku}</span>
              </div>
            </div>

            {/* Thumbnail Gallery Row */}
            <div className="grid grid-cols-4 gap-2 sm:gap-3 pt-3">
              {product.gallery.map((img, idx) => (
                <button
                  type="button"
                  key={idx}
                  onClick={() => setActiveImage(img)}
                  className={`aspect-square rounded-xl overflow-hidden border transition-all cursor-pointer ${
                    activeImage === img
                      ? 'border-[#CBAC70] ring-2 ring-[#CBAC70] scale-102'
                      : 'border-white/10 hover:border-white/40 opacity-70 hover:opacity-100'
                  }`}
                >
                  <img src={img} alt={`Gallery ${idx}`} className="w-full h-full object-cover" />
                </button>
              ))}
            </div>

          </div>
        </div>

        {/* RIGHT COLUMN: Product Specifications & Action Studio (6 cols) */}
        <div className="lg:col-span-6 space-y-5">
          <div className="rounded-2xl sm:rounded-3xl bg-[#0E1736] border border-[#CBAC70]/30 shadow-2xl p-5 sm:p-7 space-y-6">
            
            {/* Title & Category Header */}
            <div className="space-y-2 border-b border-white/10 pb-4">
              <div className="flex items-center gap-2 text-[11px]">
                <span className="font-mono text-[#CBAC70] font-bold uppercase tracking-widest">
                  {product.category} • {product.gsm ? `${product.gsm}GSM` : 'Bespoke'}
                </span>
                <span className="text-[#94A3B8]">|</span>
                <div className="flex items-center gap-1 text-[#CBAC70]">
                  <Star className="w-3.5 h-3.5 fill-current" />
                  <span className="font-bold">{product.rating}</span>
                  <span className="text-[#94A3B8]">({product.reviewCount} ulasan)</span>
                </div>
              </div>

              <h1 className="text-xl sm:text-2xl font-black text-[#FDFCFF] leading-tight uppercase">
                {product.title}
              </h1>
              
              <p className="text-xs text-[#94A3B8] leading-relaxed">
                {product.subtitle}
              </p>
            </div>

            {/* Single Unified Synchronized Price Box */}
            <div className="p-3.5 sm:p-4 rounded-xl bg-[#0B132B] border border-[#CBAC70]/30 flex items-baseline justify-between shadow-inner">
              <div className="space-y-0.5">
                <div className="flex items-baseline gap-2.5">
                  <span className="text-2xl sm:text-3xl font-black text-[#CBAC70] gold-gradient-pure">
                    {formatRupiah(currentPrice * quantity)}
                  </span>
                  {currentCompareAt && currentCompareAt > currentPrice && (
                    <span className="text-xs text-[#94A3B8] line-through">
                      {formatRupiah(currentCompareAt * quantity)}
                    </span>
                  )}
                </div>
                {quantity > 1 && (
                  <p className="text-[10px] font-mono text-[#94A3B8]">
                    ({formatRupiah(currentPrice)} × {quantity} pcs)
                  </p>
                )}
              </div>

              <span className="text-[10px] sm:text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full">
                ✓ Ready Stock ({activeVariant.availableStock || product.stockTotal} pcs)
              </span>
            </div>

            {/* 1. Colorway Selection Swatches */}
            <div className="space-y-2">
              <div className="flex justify-between items-center text-xs">
                <span className="font-bold text-[#FDFCFF]">Pilihan Warna:</span>
                <span className="text-[#CBAC70] font-semibold font-mono">{selectedColor.name}</span>
              </div>
              
              <div className="flex flex-wrap gap-2">
                {product.colors.map((c, idx) => (
                  <button
                    type="button"
                    key={idx}
                    onClick={() => handleColorChange(c)}
                    className={`px-3.5 py-2 rounded-xl border text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer ${
                      selectedColor.name === c.name
                        ? 'border-[#CBAC70] bg-[#14204A] text-[#FDFCFF] ring-2 ring-[#CBAC70] shadow-md scale-102 font-bold'
                        : 'border-white/15 hover:border-white/40 text-[#94A3B8] bg-[#0B132B]'
                    }`}
                  >
                    <span className="w-3.5 h-3.5 rounded-full border border-white/20 shrink-0" style={{ backgroundColor: c.hex }} />
                    <span>{c.name}</span>
                  </button>
                ))}
              </div>
            </div>

            {/* 2. Size Selection */}
            <div className="space-y-2">
              <div className="flex justify-between items-center text-xs">
                <span className="font-bold text-[#FDFCFF]">Pilih Ukuran:</span>
                <button
                  type="button"
                  onClick={() => setShowSizeChart(true)}
                  className="text-[#CBAC70] hover:underline flex items-center gap-1 font-semibold text-[11px] cursor-pointer"
                >
                  <Ruler className="w-3.5 h-3.5" /> Panduan Ukuran
                </button>
              </div>

              <div className="flex flex-wrap gap-2">
                {product.sizes.map((s) => (
                  <button
                    type="button"
                    key={s}
                    onClick={() => handleSizeChange(s)}
                    className={`min-w-[48px] py-2 px-3.5 rounded-xl border text-xs font-bold transition-all cursor-pointer flex items-center justify-center ${
                      selectedSize === s
                        ? 'border-[#CBAC70] bg-[#CBAC70] text-[#0B132B] shadow-md scale-102 font-black'
                        : 'border-white/15 bg-[#0B132B] text-[#94A3B8] hover:text-white hover:border-[#CBAC70]/50'
                    }`}
                  >
                    {s}
                  </button>
                ))}
              </div>
            </div>

            {/* 3. Quantity Stepper */}
            <div className="space-y-2 pt-2 border-t border-white/10">
              <span className="font-bold text-xs text-[#FDFCFF] block">Jumlah Pesanan:</span>
              <div className="flex items-center gap-3">
                <div className="flex items-center border border-white/20 rounded-xl bg-[#0B132B] overflow-hidden">
                  <button
                    type="button"
                    onClick={() => handleQuantity(-1)}
                    disabled={quantity <= 1}
                    className="w-10 h-10 flex items-center justify-center font-bold text-base text-[#94A3B8] hover:text-white hover:bg-white/5 transition disabled:opacity-30 cursor-pointer"
                  >
                    -
                  </button>
                  <span className="w-12 h-10 flex items-center justify-center text-center font-black text-sm text-[#FDFCFF] font-mono">
                    {quantity}
                  </span>
                  <button
                    type="button"
                    onClick={() => handleQuantity(1)}
                    disabled={quantity >= (activeVariant.availableStock || product.stockTotal)}
                    className="w-10 h-10 flex items-center justify-center font-bold text-base text-[#94A3B8] hover:text-white hover:bg-white/5 transition disabled:opacity-30 cursor-pointer"
                  >
                    +
                  </button>
                </div>

                <span className="text-[11px] text-[#94A3B8] font-mono">
                  Maks. {activeVariant.availableStock || product.stockTotal} pcs
                </span>
              </div>
            </div>

            {/* 4. Desktop Action Buttons */}
            <div className="hidden sm:flex items-center gap-3 pt-4 border-t border-white/10">
              <button
                type="button"
                onClick={() => toggleWishlist(product.id)}
                className={`p-3.5 rounded-xl border transition-all active:scale-95 shadow flex items-center justify-center shrink-0 cursor-pointer ${
                  isCurrentProductFavorited
                    ? 'bg-rose-500/20 border-rose-500/60 text-rose-400'
                    : 'bg-[#14204A] border-[#CBAC70]/40 text-slate-300 hover:text-rose-400'
                }`}
                title="Simpan ke Wishlist (Cache)"
              >
                <Heart className={`w-5 h-5 ${isCurrentProductFavorited ? 'fill-rose-500 text-rose-500' : ''}`} />
              </button>

              <button
                type="button"
                onClick={handleAddToBag}
                className="flex-1 py-3.5 rounded-xl bg-[#14204A] hover:bg-[#1A2A5E] border border-[#CBAC70]/40 text-[#CBAC70] font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all active:scale-95 shadow cursor-pointer"
              >
                <ShoppingBag className="w-4 h-4" />
                <span>Add to Bag</span>
              </button>

              <button
                type="button"
                onClick={handleInstantBuy}
                className="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 transition-all active:scale-95 shadow-xl cursor-pointer"
              >
                <Zap className="w-4 h-4 fill-current" />
                <span>Instant Buy</span>
              </button>
            </div>

          </div>
        </div>

      </div>

      {/* Specifications & Description (Double-Framed Card) */}
      <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/30 shadow-2xl">
        <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-6 space-y-5">
          
          <div>
            <h2 className="text-xs font-bold uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-2.5 flex items-center gap-2">
              <Sparkles className="w-4 h-4" /> Fabric & Construction Specifications
            </h2>
            
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-3.5 text-xs">
              {Object.entries(product.specifications || {}).map(([key, val], idx) => (
                <div key={idx} className="flex justify-between p-2.5 sm:p-3 rounded-xl bg-[#0B132B] border border-white/5">
                  <span className="text-[#94A3B8]">{key}</span>
                  <span className="font-bold text-[#FDFCFF]">{val}</span>
                </div>
              ))}
            </div>
          </div>

          <div className="space-y-1.5 border-t border-white/10 pt-4">
            <h3 className="font-bold text-xs sm:text-sm text-[#FDFCFF] uppercase tracking-wider">Deskripsi & Perawatan</h3>
            <p className="text-xs text-[#94A3B8] leading-relaxed whitespace-pre-line">
              {product.description}
            </p>
          </div>

        </div>
      </div>

      {/* Verified Reviews Section */}
      <div className="rounded-2xl sm:rounded-3xl bg-[#0E1736] border border-[#CBAC70]/30 p-5 sm:p-8 space-y-6 shadow-2xl">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between border-b border-white/10 pb-4 gap-3">
          <div>
            <h2 className="text-sm sm:text-base font-black text-[#FDFCFF] uppercase tracking-wider flex items-center gap-2">
              <Star className="w-4 h-4 text-[#CBAC70] fill-current" />
              <span>Ulasan Pembeli Terverifikasi ({product.reviewCount})</span>
            </h2>
            <p className="text-xs text-[#94A3B8] mt-0.5">Rating Kepuasan 4.9/5 dari pelanggan streetwear se-Indonesia</p>
          </div>

          <div className="flex items-center gap-1.5 overflow-x-auto">
            <button
              type="button"
              onClick={() => setReviewFilter('all')}
              className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer ${
                reviewFilter === 'all' ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow' : 'bg-[#0B132B] text-[#94A3B8] hover:text-white'
              }`}
            >
              Semua ({reviewsList.length})
            </button>
            <button
              type="button"
              onClick={() => setReviewFilter('photo')}
              className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-1 ${
                reviewFilter === 'photo' ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow' : 'bg-[#0B132B] text-[#94A3B8] hover:text-white'
              }`}
            >
              <ImageIcon className="w-3.5 h-3.5" />
              <span>Dengan Foto</span>
            </button>
          </div>
        </div>

        {/* Reviews List */}
        <div className="space-y-4">
          {filteredReviews.map((rev) => (
            <div key={rev.id} className="p-4 rounded-2xl bg-[#070D1F] border border-white/5 space-y-2.5">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2.5">
                  <div className="w-7 h-7 rounded-lg bg-[#14204A] border border-[#CBAC70]/40 flex items-center justify-center text-xs font-bold text-[#CBAC70]">
                    {rev.avatar}
                  </div>
                  <div>
                    <p className="font-bold text-xs text-[#FDFCFF]">{rev.author}</p>
                    <p className="text-[10px] text-[#94A3B8] font-mono">{rev.variant}</p>
                  </div>
                </div>

                <div className="flex items-center gap-1 text-[#CBAC70]">
                  {[...Array(rev.rating)].map((_, i) => (
                    <Star key={i} className="w-3.5 h-3.5 fill-current" />
                  ))}
                </div>
              </div>

              <p className="text-xs text-slate-300 leading-relaxed">{rev.comment}</p>

              {rev.photos.length > 0 && (
                <div className="flex items-center gap-2 pt-1">
                  {rev.photos.map((img, pIdx) => (
                    <img
                      key={pIdx}
                      src={img}
                      alt="Fit pic"
                      className="w-16 h-16 rounded-xl object-cover border border-white/10"
                    />
                  ))}
                </div>
              )}
            </div>
          ))}
        </div>
      </div>

      {/* Related Products Grid */}
      <div className="space-y-4">
        <div className="border-b border-white/10 pb-3 flex items-center justify-between">
          <h3 className="font-bold text-base sm:text-lg text-[#FDFCFF] uppercase tracking-wide">
            Koleksi Terkait Lainnya
          </h3>
          <Link href="/" className="text-xs font-bold text-[#CBAC70] hover:underline">
            Jelajahi Semua Koleksi →
          </Link>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-5">
          {relatedProducts.map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
        </div>
      </div>

      {/* Mobile Sticky Bottom Floating Bar (Mobile Experience Focus) */}
      <div className="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#080E20]/95 backdrop-blur-xl border-t border-[#CBAC70]/30 px-3 py-2.5 flex items-center gap-2 shadow-2xl">
        <button
          type="button"
          onClick={() => toggleWishlist(product.id)}
          className={`p-3 rounded-xl border transition-all active:scale-95 shadow flex items-center justify-center shrink-0 cursor-pointer ${
            isCurrentProductFavorited
              ? 'bg-rose-500/20 border-rose-500/60 text-rose-400'
              : 'bg-[#14204A] border-[#CBAC70]/40 text-slate-300'
          }`}
          title="Wishlist (Cache)"
        >
          <Heart className={`w-4 h-4 ${isCurrentProductFavorited ? 'fill-rose-500 text-rose-500' : ''}`} />
        </button>

        <button
          type="button"
          onClick={handleAddToBag}
          className="flex-1 py-3 bg-[#14204A] border border-[#CBAC70]/40 text-[#CBAC70] font-black text-xs uppercase rounded-xl flex items-center justify-center gap-1.5 active:scale-95 shadow cursor-pointer"
        >
          <ShoppingBag className="w-3.5 h-3.5" />
          <span>+ Bag</span>
        </button>

        <button
          type="button"
          onClick={handleInstantBuy}
          className="flex-1 py-3 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl flex items-center justify-center gap-1.5 shadow-lg active:scale-95 cursor-pointer"
        >
          <Zap className="w-3.5 h-3.5 fill-current" />
          <span>Beli ({formatRupiah(currentPrice * quantity)})</span>
        </button>
      </div>

      {/* Size Chart Modal (100% Dynamic: Abjad S-XXL vs Nomor 28-38 vs All Size) */}
      {showSizeChart && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 animate-in zoom-in-95 text-[#FDFCFF]">
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <div>
                <h3 className="font-bold text-xs sm:text-sm text-[#CBAC70] uppercase tracking-wider flex items-center gap-2">
                  <Ruler className="w-4 h-4" />
                  {isNumericSizeProduct
                    ? 'Size Chart Guide (Pants & Denim / Celana)'
                    : isAllSizeProduct
                    ? 'Size Chart Guide (All Size / Adjustable)'
                    : 'Size Chart Guide (Boxy Oversized & Tops)'}
                </h3>
                <p className="text-[10px] text-slate-400 mt-0.5">
                  Disesuaikan khusus untuk varian ukuran produk ini ({product.sizes?.join(', ') || 'Standar'})
                </p>
              </div>
              <button 
                type="button"
                onClick={() => setShowSizeChart(false)} 
                className="text-[#94A3B8] hover:text-white cursor-pointer"
              >
                ✕
              </button>
            </div>

            <div className="overflow-x-auto text-xs">
              {isNumericSizeProduct ? (
                /* TABEL UKURAN NOMOR / ANGKA (DINAMIS SESUAI PRODUCT.SIZES) */
                <table className="w-full text-left border border-white/10 rounded-lg overflow-hidden">
                  <thead className="bg-[#080E20] text-[#CBAC70] font-bold text-[11px]">
                    <tr>
                      <th className="p-2.5">Size</th>
                      <th className="p-2.5">Lingkar Pinggang</th>
                      <th className="p-2.5">Panjang Celana</th>
                      <th className="p-2.5">Lingkar Paha</th>
                      <th className="p-2.5">Open Leg</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-white/5 text-[#94A3B8]">
                    {(product.sizes && product.sizes.length > 0 ? product.sizes : ['28', '30', '32', '34', '36']).map((sizeItem, idx) => {
                      const sizeNum = parseInt(sizeItem) || 30;
                      const waist = Math.round(sizeNum * 2.54) + ' cm';
                      const length = Math.min(100 + Math.round((sizeNum - 26) * 0.7), 110) + ' cm';
                      const thigh = (48 + Math.round((sizeNum - 26) * 1.5)) + ' cm';
                      const leg = (34 + Math.round((sizeNum - 26) * 1.0)) + ' cm';
                      const isCurrent = String(selectedSize) === String(sizeItem);

                      return (
                        <tr
                          key={idx}
                          onClick={() => setSelectedSize(sizeItem)}
                          className={`cursor-pointer transition-colors ${
                            isCurrent
                              ? 'bg-[#CBAC70]/20 text-[#CBAC70] font-bold'
                              : 'hover:bg-white/5'
                          }`}
                        >
                          <td className="p-2.5 font-bold text-white flex items-center gap-1.5">
                            <span>{sizeItem}</span>
                            {isCurrent && (
                              <span className="text-[9px] px-1.5 py-0.5 rounded bg-[#CBAC70] text-[#0B132B] font-bold">
                                Dipilih
                              </span>
                            )}
                          </td>
                          <td className="p-2.5">{waist}</td>
                          <td className="p-2.5">{length}</td>
                          <td className="p-2.5">{thigh}</td>
                          <td className="p-2.5">{leg}</td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              ) : isAllSizeProduct ? (
                /* INFO UKURAN ALL SIZE / AKSESORIS */
                <div className="p-4 rounded-xl bg-[#080E20] border border-white/10 space-y-3">
                  <div className="flex items-center justify-between border-b border-white/5 pb-2">
                    <span className="text-slate-400">Ukuran Produk:</span>
                    <span className="font-bold text-[#CBAC70]">All Size / One Size Fits All</span>
                  </div>
                  <div className="text-xs text-slate-300 space-y-2 leading-relaxed">
                    <p>• <strong>Topi / Cap:</strong> Lingkar kepala 56 - 60 cm dengan strap pengatur logam (Adjustable Brass Strap).</p>
                    <p>• <strong>Tas / Bags:</strong> Dimensi kompartemen dirancang modular untuk penggunaan harian (Daily Streetwear Utility).</p>
                    <p>• <strong>Ikat Pinggang / Belt:</strong> Panjang 115 - 125 cm dengan lubang standar fleksibel.</p>
                  </div>
                </div>
              ) : (
                /* TABEL UKURAN ABJAD (DINAMIS SESUAI PRODUCT.SIZES S-XXL) */
                <table className="w-full text-left border border-white/10 rounded-lg overflow-hidden">
                  <thead className="bg-[#080E20] text-[#CBAC70] font-bold text-[11px]">
                    <tr>
                      <th className="p-2.5">Size</th>
                      <th className="p-2.5">Lebar Dada</th>
                      <th className="p-2.5">Panjang Baju</th>
                      <th className="p-2.5">Panjang Lengan</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-white/5 text-[#94A3B8]">
                    {(product.sizes && product.sizes.length > 0 ? product.sizes : ['S', 'M', 'L', 'XL', 'XXL']).map((sizeItem, idx) => {
                      const standard: Record<string, { chest: string; length: string; sleeve: string }> = {
                        'XS': { chest: '51 cm', length: '66 cm', sleeve: '23 cm' },
                        'S': { chest: '54 cm', length: '68 cm', sleeve: '24 cm' },
                        'M': { chest: '57 cm', length: '71 cm', sleeve: '25 cm' },
                        'L': { chest: '60 cm', length: '74 cm', sleeve: '26 cm' },
                        'XL': { chest: '63 cm', length: '77 cm', sleeve: '27 cm' },
                        'XXL': { chest: '66 cm', length: '80 cm', sleeve: '28 cm' },
                        '2XL': { chest: '66 cm', length: '80 cm', sleeve: '28 cm' },
                        'XXXL': { chest: '69 cm', length: '82 cm', sleeve: '29 cm' },
                        '3XL': { chest: '69 cm', length: '82 cm', sleeve: '29 cm' },
                      };
                      const upper = sizeItem.toUpperCase().trim();
                      const m = standard[upper] || { chest: '58 cm', length: '72 cm', sleeve: '25 cm' };
                      const isCurrent = String(selectedSize) === String(sizeItem);

                      return (
                        <tr
                          key={idx}
                          onClick={() => setSelectedSize(sizeItem)}
                          className={`cursor-pointer transition-colors ${
                            isCurrent
                              ? 'bg-[#CBAC70]/20 text-[#CBAC70] font-bold'
                              : 'hover:bg-white/5'
                          }`}
                        >
                          <td className="p-2.5 font-bold text-white flex items-center gap-1.5">
                            <span>{sizeItem}</span>
                            {isCurrent && (
                              <span className="text-[9px] px-1.5 py-0.5 rounded bg-[#CBAC70] text-[#0B132B] font-bold">
                                Dipilih
                              </span>
                            )}
                          </td>
                          <td className="p-2.5">{m.chest}</td>
                          <td className="p-2.5">{m.length}</td>
                          <td className="p-2.5">{m.sleeve}</td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              )}
            </div>

            <div className="flex items-center justify-between text-[10px] text-[#94A3B8]">
              <span className="italic">
                {isNumericSizeProduct
                  ? '* Ukuran standar inci celana denim/cargo internasional. Toleransi jahit 1-2 cm.'
                  : isAllSizeProduct
                  ? '* Produk dirancang fleksibel menyesuaikan bentuk pemakaian.'
                  : '* Potongan boxy cut kami sudah drop-shoulder oversized. Toleransi jahit 1-2 cm.'}
              </span>
              <span className="text-gold font-mono">Klik baris untuk memilih ukuran</span>
            </div>

            <button
              type="button"
              onClick={() => setShowSizeChart(false)}
              className="w-full py-2.5 bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-[#E3CD99] transition cursor-pointer"
            >
              Tutup Panduan
            </button>
          </div>
        </div>
      )}

    </div>
  );
}
