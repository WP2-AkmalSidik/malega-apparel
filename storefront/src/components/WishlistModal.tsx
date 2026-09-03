'use client';

import React from 'react';
import Link from 'next/link';
import { Heart, X, ShoppingBag, ArrowRight, Trash2 } from 'lucide-react';
import { useWishlist } from '../context/WishlistContext';
import { useCart } from '../context/CartContext';

export default function WishlistModal() {
  const { wishlistProducts, isWishlistOpen, setIsWishlistOpen, toggleWishlist, clearWishlist } = useWishlist();
  const { addToCart } = useCart();

  if (!isWishlistOpen) return null;

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
    <div className="fixed inset-0 z-50 overflow-hidden">
      {/* Backdrop */}
      <div 
        className="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity"
        onClick={() => setIsWishlistOpen(false)}
      />

      <div className="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div className="w-screen max-w-md bg-[#0E1736] border-l border-[#CBAC70]/30 shadow-2xl flex flex-col">
          
          {/* Header */}
          <div className="p-4 sm:p-5 border-b border-white/10 bg-[#070D1F] flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Heart className="w-5 h-5 text-rose-500 fill-rose-500" />
              <h2 className="text-base font-bold text-slate-100 uppercase tracking-wide">
                Produk Favorit ({wishlistProducts.length})
              </h2>
            </div>
            <button
              onClick={() => setIsWishlistOpen(false)}
              className="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* List of Wishlist Items */}
          <div className="flex-1 overflow-y-auto p-4 space-y-3">
            {wishlistProducts.length > 0 ? (
              wishlistProducts.map(product => (
                <div
                  key={product.id}
                  className="p-3 rounded-2xl bg-[#070D1F]/70 border border-white/5 hover:border-[#CBAC70]/40 transition space-y-3"
                >
                  <div className="flex items-center gap-3">
                    <img
                      src={product.colors[0]?.image || product.gallery[0]}
                      alt={product.title}
                      className="w-16 h-16 rounded-xl object-cover border border-white/10 bg-[#0E1736] shrink-0"
                    />
                    <div className="min-w-0 flex-1">
                      <p className="text-[10px] font-mono text-[#CBAC70] uppercase">
                        {product.category} • {product.gsm ? `${product.gsm}GSM` : 'PREMIUM'}
                      </p>
                      <Link
                        href={`/products/${product.slug}`}
                        onClick={() => setIsWishlistOpen(false)}
                        className="font-bold text-xs text-slate-100 hover:text-[#CBAC70] transition line-clamp-1 mt-0.5"
                      >
                        {product.title}
                      </Link>
                      <p className="font-mono font-bold text-xs text-[#CBAC70] mt-1">
                        Rp {product.price.toLocaleString('id-ID')}
                      </p>
                    </div>

                    <button
                      onClick={() => toggleWishlist(product.id)}
                      className="p-1.5 text-slate-500 hover:text-rose-400 rounded-lg hover:bg-white/5 transition shrink-0"
                      title="Hapus dari Favorit"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>

                  {/* Actions for this item */}
                  <div className="flex items-center gap-2 pt-1 border-t border-white/5">
                    <button
                      onClick={() => handleAddToCart(product)}
                      className="flex-1 py-2 px-3 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#A58645] hover:from-[#E3CD99] hover:to-[#CBAC70] text-[#0B132B] text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95"
                    >
                      <ShoppingBag className="w-3.5 h-3.5" />
                      <span>Tambah ke Bag</span>
                    </button>

                    <Link
                      href={`/products/${product.slug}`}
                      onClick={() => setIsWishlistOpen(false)}
                      className="py-2 px-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-semibold transition"
                    >
                      Detail
                    </Link>
                  </div>
                </div>
              ))
            ) : (
              <div className="py-16 text-center space-y-3">
                <div className="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 mx-auto">
                  <Heart className="w-7 h-7 stroke-1" />
                </div>
                <div className="space-y-1">
                  <p className="text-sm font-bold text-slate-200">Belum ada produk favorit</p>
                  <p className="text-xs text-slate-500 max-w-xs mx-auto">
                    Simpan artikel pakaian yang Anda sukai dengan menekan ikon hati pada produk.
                  </p>
                </div>
                <Link
                  href="/"
                  onClick={() => setIsWishlistOpen(false)}
                  className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] text-xs font-bold shadow transition hover:bg-[#E3CD99]"
                >
                  <span>Jelajahi Katalog</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </Link>
              </div>
            )}
          </div>

          {/* Footer Actions */}
          {wishlistProducts.length > 0 && (
            <div className="p-4 bg-[#070D1F] border-t border-white/10 flex items-center justify-between">
              <button
                onClick={clearWishlist}
                className="text-xs text-slate-400 hover:text-rose-400 transition font-mono"
              >
                Kosongkan Semua
              </button>

              <Link
                href="/favorites"
                onClick={() => setIsWishlistOpen(false)}
                className="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition"
              >
                Buka Halaman Favorit
              </Link>
            </div>
          )}

        </div>
      </div>
    </div>
  );
}
