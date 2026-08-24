'use client';

import React from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { X, Trash2, ShoppingBag, ArrowRight, ShieldCheck, Tag } from 'lucide-react';
import { useCart } from '../context/CartContext';

export default function CartDrawer() {
  const router = useRouter();
  const {
    cart,
    cartCount,
    isCartOpen,
    setIsCartOpen,
    removeFromCart,
    updateQuantity,
    subtotal,
    appliedVouchers
  } = useCart();

  if (!isCartOpen) return null;

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const handleCheckout = () => {
    setIsCartOpen(false);
    router.push('/checkout');
  };

  return (
    <div className="fixed inset-0 z-50 overflow-hidden bg-black/80 backdrop-blur-md flex justify-end animate-in fade-in">
      <div className="bg-[#0B132B] border-l border-[#CBAC70]/30 w-full max-w-md h-full shadow-2xl flex flex-col justify-between text-[#FDFCFF]">
        
        {/* Drawer Header */}
        <div className="p-5 border-b border-white/10 flex items-center justify-between bg-[#080E20]">
          <div className="flex items-center gap-2.5">
            <ShoppingBag className="w-5 h-5 text-[#CBAC70]" />
            <h3 className="font-bold text-[#FDFCFF] text-sm uppercase tracking-wider">
              Shopping Bag ({cartCount})
            </h3>
          </div>
          <button
            onClick={() => setIsCartOpen(false)}
            className="p-1.5 rounded-full hover:bg-white/10 text-[#94A3B8] hover:text-white transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Free shipping progress notice */}
        <div className="bg-[#111D42] px-5 py-2.5 border-b border-white/5 text-[11px] text-[#CBAC70] flex items-center justify-between">
          <span>✨ Complimentary Express Delivery Active</span>
          <span className="font-bold">Gratis Ongkir</span>
        </div>

        {/* Cart Item List */}
        <div className="flex-1 overflow-y-auto p-5 divide-y divide-white/5 space-y-2">
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
                onClick={() => setIsCartOpen(false)}
                className="px-6 py-2.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] rounded-xl text-xs font-black tracking-wider uppercase shadow hover:opacity-95"
              >
                Jelajahi Katalog
              </Link>
            </div>
          ) : (
            cart.map((item) => (
              <div key={item.id} className="py-4 flex gap-3.5 items-start">
                
                {/* Thumbnail */}
                <img
                  src={item.image}
                  alt={item.title}
                  className="w-18 h-22 rounded-xl object-cover border border-white/10 shrink-0 bg-[#111D42]"
                />

                {/* Details */}
                <div className="flex-1 space-y-1.5 text-xs">
                  <h4 className="font-bold text-[#FDFCFF] line-clamp-2 leading-tight">
                    {item.title}
                  </h4>
                  
                  <div className="flex items-center gap-2 text-[11px] text-[#94A3B8]">
                    <span className="bg-[#111D42] px-2 py-0.5 rounded border border-white/5 text-[#CBAC70]">
                      {item.color}
                    </span>
                    <span className="bg-[#111D42] px-2 py-0.5 rounded border border-white/5">
                      Size {item.size}
                    </span>
                  </div>

                  <div className="flex items-center justify-between pt-2">
                    <span className="font-black text-[#CBAC70] text-sm">
                      {formatRupiah(item.price)}
                    </span>

                    {/* Quantity Steppers */}
                    <div className="flex items-center gap-2">
                      <div className="flex items-center border border-white/15 rounded-lg bg-[#080E20] overflow-hidden">
                        <button
                          onClick={() => updateQuantity(item.id, item.quantity - 1)}
                          disabled={item.quantity <= 1}
                          className="w-7 h-7 flex items-center justify-center font-bold text-[#94A3B8] hover:text-white disabled:opacity-30"
                        >
                          -
                        </button>
                        <span className="w-8 h-7 flex items-center justify-center text-center font-bold text-xs text-[#FDFCFF]">
                          {item.quantity}
                        </span>
                        <button
                          onClick={() => updateQuantity(item.id, item.quantity + 1)}
                          className="w-7 h-7 flex items-center justify-center font-bold text-[#94A3B8] hover:text-white"
                        >
                          +
                        </button>
                      </div>

                      <button
                        onClick={() => removeFromCart(item.id)}
                        className="text-[#94A3B8] hover:text-red-400 p-1 transition-colors"
                        title="Remove"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </div>

                </div>
              </div>
            ))
          )}
        </div>

        {/* Bottom Checkout Section */}
        {cart.length > 0 && (
          <div className="p-5 border-t border-white/10 bg-[#080E20] space-y-4">
            
            {/* Subtotal Calculation */}
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

            {/* Proceed to Checkout Button */}
            <button
              onClick={handleCheckout}
              className="w-full py-3.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] rounded-xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg transition-all active:scale-98"
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
