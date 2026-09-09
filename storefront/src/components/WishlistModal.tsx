'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { Heart, X, ShoppingBag, ArrowRight, Trash2 } from 'lucide-react';
import { useWishlist } from '../context/WishlistContext';
import { useCart } from '../context/CartContext';
import { useFlyToCart } from '../context/FlyToCartContext';

export default function WishlistModal() {
  const { wishlistProducts, wishlistCount, isWishlistOpen, setIsWishlistOpen, toggleWishlist, clearWishlist } = useWishlist();
  const { addToCart } = useCart();
  const { triggerFly } = useFlyToCart();

  const [isClosing, setIsClosing] = useState(false);

  // Reset closing state when opened
  useEffect(() => {
    if (isWishlistOpen) {
      setIsClosing(false);
    }
  }, [isWishlistOpen]);

  // Early return: don't render if not open and not in closing animation
  if (!isWishlistOpen && !isClosing) return null;

  const isSlideIn = isWishlistOpen && !isClosing;

  const handleClose = () => {
    setIsClosing(true);
    setTimeout(() => {
      setIsWishlistOpen(false);
      setIsClosing(false);
    }, 300);
  };

  const handleAddToCart = (product: any, e?: React.MouseEvent) => {
    let startX = typeof window !== 'undefined' ? window.innerWidth / 2 : 200;
    let startY = typeof window !== 'undefined' ? window.innerHeight / 2 : 200;

    if (e && e.currentTarget) {
      const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
      startX = rect.left + rect.width / 2;
      startY = rect.top + rect.height / 2;
    }

    const img = product.colors[0]?.image || product.gallery[0];
    triggerFly(img, startX, startY);

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

  const handleProductClick = (slug: string) => {
    handleClose();
  };

  return (
    <div
      className={`fixed inset-0 z-50 overflow-hidden flex justify-end transition-all duration-300 ${
        isSlideIn ? 'bg-black/80 backdrop-blur-md' : 'bg-black/0 backdrop-blur-none pointer-events-none'
      }`}
      onClick={(e) => { if (e.target === e.currentTarget) handleClose(); }}
    >
      <div
        className={`bg-[#0B132B] border-l border-[#CBAC70]/30 w-full max-w-md h-full shadow-2xl flex flex-col justify-between text-[#FDFCFF] transition-transform duration-300 ease-in-out ${
          isSlideIn ? 'translate-x-0' : 'translate-x-full'
        }`}
      >
        
        {/* Drawer Header */}
        <div className="p-4 sm:p-5 border-b border-white/10 flex items-center justify-between bg-[#080E20]">
          <div className="flex items-center gap-2.5">
            <Heart className="w-5 h-5 text-rose-500 fill-rose-500" />
            <h3 className="font-bold text-[#FDFCFF] text-sm uppercase tracking-wider">
              Produk Favorit ({wishlistCount})
            </h3>
          </div>
          <button
            type="button"
            onClick={handleClose}
            className="p-1.5 rounded-full hover:bg-white/10 text-[#94A3B8] hover:text-white transition-colors cursor-pointer"
            aria-label="Tutup favorit"
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
                      onClick={() => handleProductClick(product.slug)}
                      className="font-bold text-xs text-slate-100 hover:text-[#CBAC70] transition line-clamp-1 mt-0.5"
                    >
                      {product.title}
                    </Link>
                    <p className="font-mono font-bold text-xs text-[#CBAC70] mt-1">
                      Rp {product.price.toLocaleString('id-ID')}
                    </p>
                  </div>

                  <button
                    type="button"
                    onClick={() => toggleWishlist(product.id)}
                    className="p-1.5 text-slate-500 hover:text-rose-400 rounded-lg hover:bg-white/5 transition shrink-0 cursor-pointer"
                    title="Hapus dari Favorit"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>

                {/* Actions for this item */}
                <div className="flex items-center gap-2 pt-1 border-t border-white/5">
                  <button
                    type="button"
                    onClick={(e) => handleAddToCart(product, e)}
                    className="flex-1 py-2 px-3 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#A58645] hover:from-[#E3CD99] hover:to-[#CBAC70] text-[#0B132B] text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95 cursor-pointer"
                  >
                    <ShoppingBag className="w-3.5 h-3.5" />
                    <span>Tambah ke Bag</span>
                  </button>

                  <Link
                    href={`/products/${product.slug}`}
                    onClick={() => handleProductClick(product.slug)}
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
                onClick={() => handleClose()}
                className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] text-xs font-bold shadow transition hover:bg-[#E3CD99] cursor-pointer"
              >
                <span>Jelajahi Katalog</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </Link>
            </div>
          )}
        </div>

        {/* Footer Actions */}
        {wishlistProducts.length > 0 && (
          <div className="p-4 bg-[#080E20] border-t border-white/10 flex items-center justify-between">
            <button
              type="button"
              onClick={clearWishlist}
              className="text-xs text-slate-400 hover:text-rose-400 transition font-mono cursor-pointer"
            >
              Kosongkan Semua
            </button>

            <Link
              href="/favorites"
              onClick={() => handleClose()}
              className="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition"
            >
              Buka Halaman Favorit
            </Link>
          </div>
        )}

      </div>
    </div>
  );
}
