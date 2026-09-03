'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { X, Trash2, ShoppingBag, ArrowRight, ShieldCheck, ChevronDown, ChevronUp } from 'lucide-react';
import { useCart } from '../context/CartContext';
import { productsCatalog } from '../data/products';

export default function CartDrawer() {
  const router = useRouter();
  const {
    cart,
    cartCount,
    isCartOpen,
    setIsCartOpen,
    removeFromCart,
    updateQuantity,
    updateCartItemVariant,
    subtotal,
  } = useCart();

  const [isClosing, setIsClosing] = useState(false);
  const [expandedItemId, setExpandedItemId] = useState<string | null>(null);

  // Reset closing state when opened
  useEffect(() => {
    if (isCartOpen) {
      setIsClosing(false);
    }
  }, [isCartOpen]);

  if (!isCartOpen && !isClosing) return null;

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const handleClose = () => {
    setIsClosing(true);
    setTimeout(() => {
      setIsCartOpen(false);
      setIsClosing(false);
    }, 300);
  };

  const handleCheckout = () => {
    handleClose();
    setTimeout(() => router.push('/checkout'), 310);
  };

  const handleProductClick = (slug: string) => {
    handleClose();
    setTimeout(() => router.push(`/products/${slug}`), 310);
  };

  const toggleVariantEditor = (itemId: string) => {
    setExpandedItemId(prev => prev === itemId ? null : itemId);
  };

  const handleVariantChange = (itemId: string, productId: string, newColor: string, newSize: string) => {
    const product = productsCatalog.find(p => p.id === productId);
    if (!product) return;

    const colorObj = product.colors.find(c => c.name === newColor);
    const colorExtra = colorObj?.priceExtra || 0;
    const sizeExtra = product.sizePriceExtra?.[newSize] || 0;
    const newPrice = product.price + colorExtra + sizeExtra;
    const newOriginalPrice = product.originalPrice ? product.originalPrice + colorExtra + sizeExtra : newPrice;
    const newImage = colorObj?.image || product.gallery[0];

    updateCartItemVariant(itemId, newColor, newSize, newPrice, newOriginalPrice, newImage);
  };

  const isSlideIn = isCartOpen && !isClosing;

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
        <div className="p-5 border-b border-white/10 flex items-center justify-between bg-[#080E20]">
          <div className="flex items-center gap-2.5">
            <ShoppingBag className="w-5 h-5 text-[#CBAC70]" />
            <h3 className="font-bold text-[#FDFCFF] text-sm uppercase tracking-wider">
              Shopping Bag ({cartCount})
            </h3>
          </div>
          <button
            type="button"
            onClick={handleClose}
            className="p-1.5 rounded-full hover:bg-white/10 text-[#94A3B8] hover:text-white transition-colors cursor-pointer"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Free shipping notice */}
        <div className="bg-[#111D42] px-5 py-2.5 border-b border-white/5 text-[11px] text-[#CBAC70] flex items-center justify-between">
          <span>✨ Complimentary Express Delivery Active</span>
          <span className="font-bold">Gratis Ongkir</span>
        </div>

        {/* Cart Item List */}
        <div className="flex-1 overflow-y-auto p-4 space-y-3">
          {cart.length === 0 ? (
            <div className="h-full flex flex-col items-center justify-center text-center p-8 space-y-4">
              <div className="w-16 h-16 rounded-2xl bg-[#111D42] border border-[#CBAC70]/30 text-[#CBAC70] flex items-center justify-center">
                <ShoppingBag className="w-8 h-8" />
              </div>
              <div className="space-y-1">
                <p className="font-bold text-[#FDFCFF] text-sm">Shopping bag Anda masih kosong</p>
                <p className="text-xs text-[#94A3B8]">Jelajahi koleksi boxy heavyweight & utility streetwear Malega.</p>
              </div>
              <Link
                href="/products"
                onClick={handleClose}
                className="px-6 py-2.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] rounded-xl text-xs font-black tracking-wider uppercase shadow hover:opacity-95"
              >
                Jelajahi Katalog
              </Link>
            </div>
          ) : (
            cart.map((item) => {
              const product = productsCatalog.find(p => p.id === item.productId);
              const isExpanded = expandedItemId === item.id;

              return (
                <div key={item.id} className="rounded-xl bg-[#080E20] border border-white/5 overflow-hidden">
                  <div className="p-3 flex gap-3 items-start">
                    {/* Clickable Thumbnail */}
                    <button
                      type="button"
                      onClick={() => handleProductClick(item.slug)}
                      className="shrink-0 cursor-pointer group"
                    >
                      <img
                        src={item.image}
                        alt={item.title}
                        className="w-16 h-20 rounded-lg object-cover border border-white/10 bg-[#111D42] group-hover:border-[#CBAC70]/60 transition-all"
                      />
                    </button>

                    {/* Details */}
                    <div className="flex-1 min-w-0 space-y-1.5 text-xs">
                      <button
                        type="button"
                        onClick={() => handleProductClick(item.slug)}
                        className="text-left cursor-pointer"
                      >
                        <h4 className="font-bold text-[#FDFCFF] line-clamp-2 leading-tight hover:text-[#CBAC70] transition-colors">
                          {item.title}
                        </h4>
                      </button>
                      
                      {/* Variant badges + toggle */}
                      <div className="flex items-center gap-1.5 flex-wrap">
                        <span className="bg-[#111D42] px-2 py-0.5 rounded border border-white/5 text-[#CBAC70] text-[10px]">
                          {item.color}
                        </span>
                        <span className="bg-[#111D42] px-2 py-0.5 rounded border border-white/5 text-[10px] text-[#94A3B8]">
                          {item.size}
                        </span>
                        {product && (product.colors.length > 1 || product.sizes.length > 1) && (
                          <button
                            type="button"
                            onClick={() => toggleVariantEditor(item.id)}
                            className="text-[9px] font-mono text-[#CBAC70] hover:underline flex items-center gap-0.5 cursor-pointer"
                          >
                            Ubah
                            {isExpanded
                              ? <ChevronUp className="w-3 h-3" />
                              : <ChevronDown className="w-3 h-3" />
                            }
                          </button>
                        )}
                      </div>

                      <div className="flex items-center justify-between pt-1">
                        <span className="font-black text-[#CBAC70] text-sm">
                          {formatRupiah(item.price * item.quantity)}
                          {item.quantity > 1 && (
                            <span className="text-[10px] font-normal text-[#94A3B8] ml-1">
                              ({formatRupiah(item.price)} × {item.quantity})
                            </span>
                          )}
                        </span>

                        {/* Qty + Delete */}
                        <div className="flex items-center gap-1.5">
                          <div className="flex items-center border border-white/15 rounded-lg bg-[#0B132B] overflow-hidden">
                            <button
                              type="button"
                              onClick={() => updateQuantity(item.id, item.quantity - 1)}
                              disabled={item.quantity <= 1}
                              className="w-6 h-6 flex items-center justify-center font-bold text-[#94A3B8] hover:text-white disabled:opacity-30 text-xs cursor-pointer"
                            >
                              -
                            </button>
                            <span className="w-7 h-6 flex items-center justify-center text-center font-bold text-[10px] text-[#FDFCFF] font-mono">
                              {item.quantity}
                            </span>
                            <button
                              type="button"
                              onClick={() => updateQuantity(item.id, item.quantity + 1)}
                              className="w-6 h-6 flex items-center justify-center font-bold text-[#94A3B8] hover:text-white text-xs cursor-pointer"
                            >
                              +
                            </button>
                          </div>

                          <button
                            type="button"
                            onClick={() => removeFromCart(item.id)}
                            className="text-[#94A3B8] hover:text-red-400 p-1 transition-colors cursor-pointer"
                            title="Hapus item"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Expandable Variant Selector */}
                  {isExpanded && product && (
                    <div className="px-3 pb-3 pt-1 border-t border-white/5 space-y-2">
                      {/* Color Options */}
                      {product.colors.length > 1 && (
                        <div className="space-y-1">
                          <span className="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">Warna</span>
                          <div className="flex flex-wrap gap-1.5">
                            {product.colors.map((c, idx) => (
                              <button
                                type="button"
                                key={idx}
                                onClick={() => handleVariantChange(item.id, item.productId, c.name, item.size)}
                                className={`px-2.5 py-1 rounded-lg border text-[10px] font-semibold flex items-center gap-1.5 transition-all cursor-pointer ${
                                  item.color === c.name
                                    ? 'border-[#CBAC70] bg-[#14204A] text-[#FDFCFF] ring-1 ring-[#CBAC70]'
                                    : 'border-white/10 text-[#94A3B8] hover:border-white/30'
                                }`}
                              >
                                <span className="w-2.5 h-2.5 rounded-full shrink-0" style={{ backgroundColor: c.hex }} />
                                <span className="truncate max-w-[80px]">{c.name}</span>
                              </button>
                            ))}
                          </div>
                        </div>
                      )}

                      {/* Size Options */}
                      {product.sizes.length > 1 && (
                        <div className="space-y-1">
                          <span className="text-[10px] font-bold text-[#94A3B8] uppercase tracking-wider">Ukuran</span>
                          <div className="flex flex-wrap gap-1.5">
                            {product.sizes.map((s) => (
                              <button
                                type="button"
                                key={s}
                                onClick={() => handleVariantChange(item.id, item.productId, item.color, s)}
                                className={`min-w-[36px] py-1 px-2 rounded-lg border text-[10px] font-bold text-center transition-all cursor-pointer ${
                                  item.size === s
                                    ? 'border-[#CBAC70] bg-[#CBAC70] text-[#0B132B]'
                                    : 'border-white/10 text-[#94A3B8] hover:border-white/30'
                                }`}
                              >
                                {s}
                              </button>
                            ))}
                          </div>
                        </div>
                      )}
                    </div>
                  )}
                </div>
              );
            })
          )}
        </div>

        {/* Bottom Checkout Section */}
        {cart.length > 0 && (
          <div className="p-5 border-t border-white/10 bg-[#080E20] space-y-4">
            
            {/* Subtotal */}
            <div className="space-y-1.5 text-xs text-[#94A3B8]">
              <div className="flex justify-between items-center">
                <span>Subtotal ({cartCount} item):</span>
                <span className="text-sm font-black text-[#CBAC70]">{formatRupiah(subtotal)}</span>
              </div>
              <div className="flex justify-between items-center text-[11px] text-[#CBAC70]">
                <span>Pajak & Pengiriman:</span>
                <span>Dihitung saat Checkout</span>
              </div>
            </div>

            {/* Checkout Button */}
            <button
              type="button"
              onClick={handleCheckout}
              className="w-full py-3.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] rounded-xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg transition-all active:scale-98 cursor-pointer"
            >
              <span>Proceed to Checkout</span>
              <ArrowRight className="w-4 h-4" />
            </button>

            <div className="flex items-center justify-center gap-2 text-[10px] text-[#94A3B8]">
              <ShieldCheck className="w-3.5 h-3.5 text-[#CBAC70]" />
              <span>100% Original Guarantee • Encrypted Checkout</span>
            </div>

          </div>
        )}

      </div>
    </div>
  );
}
