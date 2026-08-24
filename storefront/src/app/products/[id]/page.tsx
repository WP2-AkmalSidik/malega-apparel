'use client';

import React, { useState, use } from 'react';
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
  Image as ImageIcon
} from 'lucide-react';
import { productsCatalog } from '../../../data/products';
import { useCart } from '../../../context/CartContext';
import ProductCard from '../../../components/ProductCard';

interface PageProps {
  params: Promise<{ id: string }>;
}

export default function ProductDetailPage({ params }: PageProps) {
  const resolvedParams = use(params);
  const router = useRouter();
  const { addToCart } = useCart();

  const product = productsCatalog.find(p => p.id === resolvedParams.id || p.slug === resolvedParams.id) || productsCatalog[0];

  const [selectedColor, setSelectedColor] = useState(product.colors[0]);
  const [selectedSize, setSelectedSize] = useState(product.sizes[0] || 'L');
  const [quantity, setQuantity] = useState(1);
  const [activeImage, setActiveImage] = useState(product.gallery[0] || product.colors[0].image);
  const [showSizeChart, setShowSizeChart] = useState(false);
  const [isFavorited, setIsFavorited] = useState(false);
  const [addedToast, setAddedToast] = useState(false);
  const [reviewFilter, setReviewFilter] = useState<'all' | 'photo' | '5star'>('all');

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const handleColorChange = (color: typeof product.colors[0]) => {
    setSelectedColor(color);
    setActiveImage(color.image);
  };

  const handleQuantity = (delta: number) => {
    const next = quantity + delta;
    if (next >= 1 && next <= product.stockTotal) {
      setQuantity(next);
    }
  };

  const handleAddToBag = () => {
    addToCart({
      productId: product.id,
      slug: product.slug,
      title: product.title,
      color: selectedColor.name,
      size: selectedSize,
      price: product.price,
      originalPrice: product.originalPrice,
      quantity,
      image: selectedColor.image
    });

    setAddedToast(true);
    setTimeout(() => setAddedToast(false), 2500);
  };

  const handleInstantBuy = () => {
    addToCart({
      productId: product.id,
      slug: product.slug,
      title: product.title,
      color: selectedColor.name,
      size: selectedSize,
      price: product.price,
      originalPrice: product.originalPrice,
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
      variant: `${selectedColor.name} • Size L`,
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
      variant: 'Vintage Black • Size XL',
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

  const relatedProducts = productsCatalog
    .filter(p => p.id !== product.id && (p.category === product.category || p.isBestSeller))
    .slice(0, 4);

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-8 sm:space-y-12 pb-24 md:pb-12">
      
      {/* Toast Notification */}
      {addedToast && (
        <div className="fixed top-20 right-4 sm:right-8 z-50 bg-[#111D42] text-[#FDFCFF] border border-[#CBAC70] px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 animate-in slide-in-from-top-2">
          <div className="w-6 h-6 rounded-full bg-[#CBAC70] text-[#0B132B] flex items-center justify-center font-bold text-xs">
            ✓
          </div>
          <div className="text-xs">
            <p className="font-bold text-[#FDFCFF]">Berhasil ditambahkan ke Shopping Bag!</p>
            <p className="text-[11px] text-[#CBAC70]">{selectedColor.name} • Size {selectedSize} ({quantity}x)</p>
          </div>
        </div>
      )}

      {/* Breadcrumbs */}
      <nav className="flex items-center gap-1.5 text-[11px] text-[#94A3B8] overflow-x-auto whitespace-nowrap">
        <Link href="/" className="hover:text-[#CBAC70]">Store</Link>
        <ChevronRight className="w-3 h-3 opacity-50" />
        <Link href="/products" className="hover:text-[#CBAC70]">Katalog</Link>
        <ChevronRight className="w-3 h-3 opacity-50" />
        <span className="text-[#FDFCFF] font-semibold truncate max-w-xs">{product.title}</span>
      </nav>

      {/* Main Product Showcase Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-10">
        
        {/* Left Gallery with Nested Double Card */}
        <div className="lg:col-span-6 space-y-3 sm:space-y-4">
          
          {/* Outer Card Frame */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-3 border border-[#CBAC70]/30 shadow-2xl">
            
            {/* Inner Image Canvas */}
            <div className="aspect-[4/5] rounded-xl sm:rounded-2xl overflow-hidden bg-[#050914] border border-white/10 relative group">
              <img
                src={activeImage}
                alt={product.title}
                className="w-full h-full object-cover group-hover:scale-104 transition-transform duration-500"
              />

              {/* Badges */}
              <div className="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                {product.isNewDrop && (
                  <span className="bg-[#CBAC70] text-[#0B132B] font-black text-[9px] sm:text-[10px] tracking-widest uppercase px-2.5 py-0.5 rounded shadow">
                    SS26 DROP
                  </span>
                )}
                {product.discountPercentage > 0 && (
                  <span className="bg-[#0B132B]/90 border border-[#CBAC70]/50 text-[#CBAC70] text-[10px] font-black px-2 py-0.5 rounded shadow">
                    -{product.discountPercentage}% OFF
                  </span>
                )}
              </div>

              <button
                onClick={() => setIsFavorited(!isFavorited)}
                className="absolute top-3 right-3 p-2.5 rounded-full bg-[#0B132B]/80 hover:bg-[#0B132B] border border-white/20 text-white transition-colors"
                title="Favoritkan"
              >
                <Heart className={`w-4 h-4 ${isFavorited ? 'fill-[#CBAC70] text-[#CBAC70]' : ''}`} />
              </button>
            </div>

          </div>

          {/* Thumbnails Reel */}
          <div className="grid grid-cols-4 gap-2 sm:gap-3">
            {product.gallery.map((img, idx) => (
              <button
                key={idx}
                onClick={() => setActiveImage(img)}
                className={`aspect-square rounded-xl border-2 overflow-hidden bg-[#070D1F] transition-all ${
                  activeImage === img
                    ? 'border-[#CBAC70] ring-2 ring-[#CBAC70]/40 scale-96'
                    : 'border-white/10 hover:border-white/40'
                }`}
              >
                <img src={img} alt={`Gallery ${idx}`} className="w-full h-full object-cover" />
              </button>
            ))}
          </div>
        </div>

        {/* Right Info & Purchasing Panel (Outer & Inner Card Packaging) */}
        <div className="lg:col-span-6 rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/30 shadow-2xl flex flex-col justify-between">
          
          <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-6 space-y-4 sm:space-y-5 flex-1">
            
            {/* Header info */}
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

            {/* Price Box */}
            <div className="p-3.5 sm:p-4 rounded-xl bg-[#0B132B] border border-[#CBAC70]/30 flex items-baseline justify-between shadow-inner">
              <div className="flex items-baseline gap-2.5">
                <span className="text-2xl sm:text-3xl font-black text-[#CBAC70] gold-gradient-pure">
                  {formatRupiah(product.price)}
                </span>
                {product.originalPrice > product.price && (
                  <span className="text-xs text-[#94A3B8] line-through">
                    {formatRupiah(product.originalPrice)}
                  </span>
                )}
              </div>

              <span className="text-[10px] sm:text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full">
                ✓ Ready Stock ({product.stockTotal} pcs)
              </span>
            </div>

            {/* Colorway Selection */}
            <div className="space-y-2">
              <div className="flex justify-between items-center text-xs">
                <span className="font-bold text-[#FDFCFF]">Warna:</span>
                <span className="text-[#CBAC70] font-semibold">{selectedColor.name}</span>
              </div>
              
              <div className="flex flex-wrap gap-2">
                {product.colors.map((c, idx) => (
                  <button
                    key={idx}
                    onClick={() => handleColorChange(c)}
                    className={`px-3 py-1.5 rounded-xl border text-xs font-semibold flex items-center gap-2 transition-all ${
                      selectedColor.name === c.name
                        ? 'border-[#CBAC70] bg-[#14204A] text-[#FDFCFF] ring-1 ring-[#CBAC70] shadow'
                        : 'border-white/10 hover:border-white/30 text-[#94A3B8] bg-[#0B132B]'
                    }`}
                  >
                    <span className="w-3.5 h-3.5 rounded-full border border-white/20" style={{ backgroundColor: c.hex }} />
                    <span>{c.name}</span>
                  </button>
                ))}
              </div>
            </div>

            {/* Size Selection */}
            <div className="space-y-2">
              <div className="flex justify-between items-center text-xs">
                <span className="font-bold text-[#FDFCFF]">Ukuran:</span>
                <button
                  onClick={() => setShowSizeChart(true)}
                  className="text-[#CBAC70] hover:underline flex items-center gap-1 font-semibold text-[11px]"
                >
                  <Ruler className="w-3.5 h-3.5" /> Panduan Ukuran
                </button>
              </div>

              <div className="flex flex-wrap gap-1.5 sm:gap-2">
                {product.sizes.map((s) => (
                  <button
                    key={s}
                    onClick={() => setSelectedSize(s)}
                    className={`min-w-[42px] sm:min-w-[48px] py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-xl border text-xs font-bold transition-all ${
                      selectedSize === s
                        ? 'border-[#CBAC70] bg-[#CBAC70] text-[#0B132B] shadow-md scale-102'
                        : 'border-white/15 bg-[#0B132B] text-[#94A3B8] hover:text-white hover:border-[#CBAC70]/50'
                    }`}
                  >
                    {s}
                  </button>
                ))}
              </div>
            </div>

            {/* Quantity Stepper */}
            <div className="space-y-2 pt-2 border-t border-white/10">
              <span className="font-bold text-xs text-[#FDFCFF] block">Jumlah:</span>
              <div className="flex items-center gap-4">
                <div className="flex items-center border border-white/20 rounded-xl bg-[#0B132B] overflow-hidden">
                  <button
                    onClick={() => handleQuantity(-1)}
                    disabled={quantity <= 1}
                    className="w-9 h-9 flex items-center justify-center font-bold text-base text-[#94A3B8] hover:text-white disabled:opacity-30"
                  >
                    -
                  </button>
                  <span className="w-10 h-9 flex items-center justify-center text-center font-black text-xs text-[#FDFCFF]">
                    {quantity}
                  </span>
                  <button
                    onClick={() => handleQuantity(1)}
                    disabled={quantity >= product.stockTotal}
                    className="w-9 h-9 flex items-center justify-center font-bold text-base text-[#94A3B8] hover:text-white disabled:opacity-30"
                  >
                    +
                  </button>
                </div>

                <span className="text-xs text-[#94A3B8]">
                  Subtotal: <strong className="text-[#CBAC70] font-mono">{formatRupiah(product.price * quantity)}</strong>
                </span>
              </div>
            </div>

            {/* Desktop Action Buttons */}
            <div className="hidden sm:grid grid-cols-2 gap-3 pt-4 border-t border-white/10">
              <button
                onClick={handleAddToBag}
                className="py-3.5 rounded-xl bg-[#14204A] hover:bg-[#1A2A5E] border border-[#CBAC70]/40 text-[#CBAC70] font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all active:scale-95 shadow"
              >
                <ShoppingBag className="w-4 h-4" />
                <span>Add to Bag</span>
              </button>

              <button
                onClick={handleInstantBuy}
                className="py-3.5 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 transition-all active:scale-95 shadow-xl"
              >
                <span>Instant Buy</span>
                <ArrowRight className="w-4 h-4" />
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
              {Object.entries(product.specifications).map(([key, val], idx) => (
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

          {product.features && (
            <div className="space-y-1.5 border-t border-white/10 pt-4">
              <h3 className="font-bold text-xs sm:text-sm text-[#FDFCFF] uppercase tracking-wider">Keunggulan Konstruksi</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                {product.features.map((feat, idx) => (
                  <div key={idx} className="flex items-center gap-2 text-[#94A3B8]">
                    <span className="w-1.5 h-1.5 rounded-full bg-[#CBAC70]" />
                    <span>{feat}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

        </div>
      </div>

      {/* NEW: Customer Reviews & Ratings Section (Double-Framed Card) */}
      <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/30 shadow-2xl">
        <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-6 space-y-6">
          
          {/* Section Header & Rating Overview */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-4">
            <div className="space-y-1">
              <h2 className="text-sm sm:text-base font-black uppercase tracking-wide text-[#FDFCFF] flex items-center gap-2">
                <span>Ulasan Pembeli</span>
                <span className="text-xs text-[#CBAC70] font-normal font-mono">({product.reviewCount} ulasan)</span>
              </h2>
              <p className="text-xs text-[#94A3B8]">100% Ulasan dari pembeli terverifikasi Malega Studio</p>
            </div>

            {/* Score Pill */}
            <div className="flex items-center gap-3 bg-[#0B132B] border border-[#CBAC70]/30 p-3 rounded-2xl shrink-0 self-start sm:self-auto">
              <div className="text-2xl font-black text-[#CBAC70] gold-gradient-pure leading-none">
                {product.rating}
              </div>
              <div className="space-y-1">
                <div className="flex text-[#CBAC70]">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-3.5 h-3.5 fill-current" />
                  ))}
                </div>
                <p className="text-[10px] text-[#94A3B8] font-medium">99% Pembeli Puas</p>
              </div>
            </div>
          </div>

          {/* Review Filter Tabs */}
          <div className="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1 text-xs">
            <button
              onClick={() => setReviewFilter('all')}
              className={`px-3 py-1.5 rounded-xl font-bold transition-all shrink-0 ${
                reviewFilter === 'all'
                  ? 'bg-[#CBAC70] text-[#0B132B]'
                  : 'bg-[#0B132B] border border-white/10 text-[#94A3B8] hover:text-white'
              }`}
            >
              Semua Ulasan ({reviewsList.length})
            </button>

            <button
              onClick={() => setReviewFilter('photo')}
              className={`px-3 py-1.5 rounded-xl font-bold transition-all shrink-0 flex items-center gap-1.5 ${
                reviewFilter === 'photo'
                  ? 'bg-[#CBAC70] text-[#0B132B]'
                  : 'bg-[#0B132B] border border-white/10 text-[#94A3B8] hover:text-white'
              }`}
            >
              <ImageIcon className="w-3.5 h-3.5" />
              <span>Dengan Foto ({reviewsList.filter(r => r.photos.length > 0).length})</span>
            </button>

            <button
              onClick={() => setReviewFilter('5star')}
              className={`px-3 py-1.5 rounded-xl font-bold transition-all shrink-0 flex items-center gap-1.5 ${
                reviewFilter === '5star'
                  ? 'bg-[#CBAC70] text-[#0B132B]'
                  : 'bg-[#0B132B] border border-white/10 text-[#94A3B8] hover:text-white'
              }`}
            >
              <Star className="w-3.5 h-3.5 fill-current text-[#CBAC70]" />
              <span>Bintang 5 ({reviewsList.filter(r => r.rating === 5).length})</span>
            </button>
          </div>

          {/* Reviews List */}
          <div className="divide-y divide-white/5 space-y-4">
            {filteredReviews.map((rev) => (
              <div key={rev.id} className="pt-4 space-y-2.5 text-xs">
                
                {/* Author row */}
                <div className="flex items-start justify-between gap-3">
                  <div className="flex items-center gap-2.5">
                    <div className="w-8 h-8 rounded-full bg-[#14204A] border border-[#CBAC70]/40 text-[#CBAC70] font-black flex items-center justify-center text-xs shrink-0">
                      {rev.avatar}
                    </div>
                    <div>
                      <div className="flex items-center gap-1.5">
                        <span className="font-bold text-[#FDFCFF]">{rev.author}</span>
                        <span className="inline-flex items-center gap-0.5 text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.2 rounded border border-emerald-500/20">
                          <CheckCircle className="w-2.5 h-2.5" /> Terverifikasi
                        </span>
                      </div>
                      <span className="text-[10px] text-[#94A3B8]">{rev.date}</span>
                    </div>
                  </div>

                  {/* Stars */}
                  <div className="flex text-[#CBAC70] shrink-0">
                    {[...Array(rev.rating)].map((_, i) => (
                      <Star key={i} className="w-3 h-3 fill-current" />
                    ))}
                  </div>
                </div>

                {/* Variant & Fitting info */}
                <div className="p-2 rounded-lg bg-[#0B132B] border border-white/5 text-[11px] text-[#94A3B8] flex flex-wrap items-center gap-x-3 gap-y-1">
                  <span><strong className="text-white">Varian:</strong> {rev.variant}</span>
                  <span><strong className="text-white">Fitting:</strong> {rev.bodyProfile}</span>
                </div>

                {/* Comment */}
                <p className="text-xs text-[#FDFCFF]/90 leading-relaxed">
                  {rev.comment}
                </p>

                {/* Photos if any */}
                {rev.photos.length > 0 && (
                  <div className="flex items-center gap-2 pt-1 overflow-x-auto">
                    {rev.photos.map((photo, pIdx) => (
                      <img
                        key={pIdx}
                        src={photo}
                        alt="Customer Fit Pic"
                        className="w-16 h-20 rounded-xl object-cover border border-white/10 shrink-0 hover:scale-105 transition-transform"
                      />
                    ))}
                  </div>
                )}

                {/* Helpful count */}
                <div className="pt-1 flex items-center gap-1 text-[10px] text-[#94A3B8]">
                  <button className="flex items-center gap-1 hover:text-[#CBAC70] transition-colors">
                    <ThumbsUp className="w-3 h-3" />
                    <span>Membantu ({rev.helpful})</span>
                  </button>
                </div>

              </div>
            ))}
          </div>

        </div>
      </div>

      {/* Related Products Grid */}
      <div className="space-y-4">
        <div className="border-b border-white/10 pb-3 flex items-center justify-between">
          <h3 className="font-bold text-base sm:text-lg text-[#FDFCFF] uppercase tracking-wide">
            Koleksi Terkait
          </h3>
          <Link href="/products" className="text-xs font-bold text-[#CBAC70] hover:underline">
            Lihat Semua →
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
          onClick={handleAddToBag}
          className="flex-1 py-3 bg-[#14204A] border border-[#CBAC70]/40 text-[#CBAC70] font-black text-xs uppercase rounded-xl flex items-center justify-center gap-1.5 active:scale-95 shadow"
        >
          <ShoppingBag className="w-3.5 h-3.5" />
          <span>+ Bag</span>
        </button>

        <button
          onClick={handleInstantBuy}
          className="flex-1 py-3 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl flex items-center justify-center gap-1.5 shadow-lg active:scale-95"
        >
          <Zap className="w-3.5 h-3.5 fill-current" />
          <span>Beli ({formatRupiah(product.price * quantity)})</span>
        </button>
      </div>

      {/* Size Chart Modal */}
      {showSizeChart && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 animate-in zoom-in-95 text-[#FDFCFF]">
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <h3 className="font-bold text-xs sm:text-sm text-[#CBAC70] uppercase tracking-wider flex items-center gap-2">
                <Ruler className="w-4 h-4" /> Size Chart Guide (Boxy Oversized)
              </h3>
              <button onClick={() => setShowSizeChart(false)} className="text-[#94A3B8] hover:text-white">✕</button>
            </div>

            <div className="overflow-x-auto text-xs">
              <table className="w-full text-left border border-white/10 rounded-lg overflow-hidden">
                <thead className="bg-[#080E20] text-[#CBAC70] font-bold">
                  <tr>
                    <th className="p-2.5">Size</th>
                    <th className="p-2.5">Lebar Dada</th>
                    <th className="p-2.5">Panjang Baju</th>
                    <th className="p-2.5">Panjang Lengan</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-white/5 text-[#94A3B8]">
                  <tr><td className="p-2.5 font-bold text-white">S</td><td className="p-2.5">54 cm</td><td className="p-2.5">68 cm</td><td className="p-2.5">24 cm</td></tr>
                  <tr><td className="p-2.5 font-bold text-white">M</td><td className="p-2.5">57 cm</td><td className="p-2.5">71 cm</td><td className="p-2.5">25 cm</td></tr>
                  <tr className="bg-[#CBAC70]/10 text-[#CBAC70] font-bold"><td className="p-2.5">L (Model Standard)</td><td className="p-2.5">60 cm</td><td className="p-2.5">74 cm</td><td className="p-2.5">26 cm</td></tr>
                  <tr><td className="p-2.5 font-bold text-white">XL</td><td className="p-2.5">63 cm</td><td className="p-2.5">77 cm</td><td className="p-2.5">27 cm</td></tr>
                  <tr><td className="p-2.5 font-bold text-white">XXL</td><td className="p-2.5">66 cm</td><td className="p-2.5">80 cm</td><td className="p-2.5">28 cm</td></tr>
                </tbody>
              </table>
            </div>

            <button
              onClick={() => setShowSizeChart(false)}
              className="w-full py-2.5 bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-[#E3CD99]"
            >
              Tutup Panduan
            </button>
          </div>
        </div>
      )}

    </div>
  );
}
