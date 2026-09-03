'use client';

import React from 'react';
import Link from 'next/link';
import { Heart, ShoppingBag, ArrowRight, Trash2, ArrowLeft, Layers } from 'lucide-react';
import { useWishlist } from '../../context/WishlistContext';
import { useCart } from '../../context/CartContext';

export default function FavoritesPage() {
  const { wishlistProducts, toggleWishlist, clearWishlist } = useWishlist();
  const { addToCart } = useCart();

  const handleAddToCart = (product: any) => {
    addToCart({
      productId: product.id,
      slug: product.slug,
      title: product.title,
      color: product.colors[0]?.name || 'Standard',
      size: product.sizes[0] || 'L',
      price: product.price,
      originalPrice: product.originalPrice,
      quantity: 1,
      image: product.colors[0]?.image || product.gallery[0]
    });
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">
      {/* Header Banner */}
      <div className="rounded-3xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-6 border border-[#CBAC70]/30 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="space-y-1">
          <div className="flex items-center gap-2">
            <Heart className="w-5 h-5 text-rose-500 fill-rose-500" />
            <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-[#CBAC70]">
              WISHLIST & KOLEKSI FAVORIT
            </span>
          </div>
          <h1 className="text-2xl sm:text-3xl font-black text-white uppercase tracking-wide">
            Daftar Produk Favorit Anda
          </h1>
          <p className="text-xs sm:text-sm text-slate-400 max-w-lg">
            Simpan produk impian Anda untuk dibeli kapan saja. Semua produk disimpan secara aman di akun Anda.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <Link
            href="/"
            className="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 hover:border-[#CBAC70] text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2"
          >
            <ArrowLeft className="w-4 h-4" />
            <span>Katalog Utama</span>
          </Link>

          {wishlistProducts.length > 0 && (
            <button
              onClick={clearWishlist}
              className="px-4 py-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 hover:bg-rose-500/20 text-rose-300 text-xs font-bold transition"
            >
              Kosongkan Semua
            </button>
          )}
        </div>
      </div>

      {/* Grid of Wishlist Items */}
      {wishlistProducts.length > 0 ? (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          {wishlistProducts.map(product => (
            <div
              key={product.id}
              className="rounded-3xl bg-[#0E1736] border border-white/10 hover:border-[#CBAC70]/50 shadow-xl overflow-hidden flex flex-col justify-between group transition duration-300"
            >
              {/* Product Thumbnail & Category */}
              <div className="relative aspect-square overflow-hidden bg-[#070D1F]">
                <img
                  src={product.colors[0]?.image || product.gallery[0]}
                  alt={product.title}
                  className="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                />

                {/* Badge */}
                <div className="absolute top-3 left-3 flex flex-col gap-1">
                  <span className="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold bg-[#0B132B]/80 text-[#CBAC70] border border-[#CBAC70]/30 backdrop-blur-sm">
                    {product.category}
                  </span>
                  {product.gsm && (
                    <span className="px-2 py-0.5 rounded-lg text-[10px] font-mono font-medium bg-black/60 text-white backdrop-blur-sm">
                      {product.gsm}GSM
                    </span>
                  )}
                </div>

                {/* Remove Heart Button */}
                <button
                  onClick={() => toggleWishlist(product.id)}
                  className="absolute top-3 right-3 p-2 rounded-full bg-black/60 hover:bg-rose-500/80 text-rose-500 hover:text-white backdrop-blur-sm transition shadow"
                  title="Hapus dari Wishlist"
                >
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>

              {/* Product Info */}
              <div className="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-3">
                <div className="space-y-1">
                  <Link
                    href={`/products/${product.slug}`}
                    className="font-bold text-sm text-slate-100 group-hover:text-[#CBAC70] transition line-clamp-2 leading-snug"
                  >
                    {product.title}
                  </Link>
                  <p className="text-xs text-slate-400 line-clamp-1">
                    {product.subtitle}
                  </p>
                </div>

                <div className="space-y-2 pt-2 border-t border-white/5">
                  <div className="flex items-baseline justify-between">
                    <div>
                      <p className="font-bold font-mono text-base text-[#CBAC70]">
                        Rp {product.price.toLocaleString('id-ID')}
                      </p>
                      {product.originalPrice > product.price && (
                        <p className="font-mono text-xs text-slate-500 line-through">
                          Rp {product.originalPrice.toLocaleString('id-ID')}
                        </p>
                      )}
                    </div>
                    <span className="text-[10px] font-mono text-emerald-400">
                      ✓ Ready Stock
                    </span>
                  </div>

                  {/* Actions */}
                  <div className="flex items-center gap-2 pt-2">
                    <button
                      onClick={() => handleAddToCart(product)}
                      className="flex-1 py-2.5 px-3 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#A58645] hover:from-[#E3CD99] hover:to-[#CBAC70] text-[#0B132B] text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-md active:scale-95"
                    >
                      <ShoppingBag className="w-4 h-4" />
                      <span>Tambah ke Bag</span>
                    </button>

                    <Link
                      href={`/products/${product.slug}`}
                      className="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold transition"
                    >
                      Detail
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="py-20 text-center space-y-4 rounded-3xl bg-[#0E1736] border border-white/5 p-8">
          <div className="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mx-auto">
            <Heart className="w-8 h-8 stroke-1" />
          </div>
          <div className="space-y-1">
            <h3 className="text-lg font-bold text-slate-200">Belum Ada Produk di Wishlist</h3>
            <p className="text-xs sm:text-sm text-slate-400 max-w-md mx-auto">
              Simpan produk favorit Anda saat menjelajah katalog dengan menekan tombol hati untuk mempermudah pembelian nanti.
            </p>
          </div>
          <Link
            href="/"
            className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#A58645] text-[#0B132B] font-bold text-xs shadow-lg transition hover:from-[#E3CD99] hover:to-[#CBAC70]"
          >
            <span>Mulai Jelajahi Produk</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      )}
    </div>
  );
}
