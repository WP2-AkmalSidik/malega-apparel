'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { CartItem, Voucher, Address, ShippingOption, PaymentMethod, OrderReceipt, Product } from '../types';
import { productsCatalog, availableVouchers, defaultAddress, shippingCouriers, paymentGateways } from '../data/products';

interface CartContextType {
  cart: CartItem[];
  cartCount: number;
  isCartOpen: boolean;
  setIsCartOpen: (open: boolean) => void;
  
  // Cart Actions
  addToCart: (item: Omit<CartItem, 'id' | 'selected'>) => void;
  removeFromCart: (id: string) => void;
  updateQuantity: (id: string, qty: number) => void;
  clearCart: () => void;
  
  // Checkout Details
  selectedAddress: Address;
  setSelectedAddress: (addr: Address) => void;
  selectedShipping: ShippingOption;
  setSelectedShipping: (ship: ShippingOption) => void;
  selectedPayment: PaymentMethod;
  setSelectedPayment: (pay: PaymentMethod) => void;
  vouchers: Voucher[];
  appliedVouchers: Voucher[];
  toggleVoucher: (code: string) => void;
  applyVoucherCode: (code: string) => boolean;
  buyerNote: string;
  setBuyerNote: (note: string) => void;

  // Financial Calculations
  subtotal: number;
  shippingCost: number;
  shippingDiscount: number;
  productDiscount: number;
  serviceFee: number;
  grandTotal: number;

  // Order Placement
  lastOrder: OrderReceipt | null;
  createOrder: () => OrderReceipt;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [cart, setCart] = useState<CartItem[]>([
    {
      id: 'init-item-1',
      productId: 'mlg-001',
      slug: 'obsidian-heavyweight-boxy-tee-300gsm',
      title: 'Obsidian Heavyweight Boxy Tee 300GSM',
      color: 'Onyx Black',
      size: 'L',
      price: 229000,
      originalPrice: 289000,
      quantity: 1,
      image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
      selected: true
    }
  ]);

  const [isCartOpen, setIsCartOpen] = useState(false);
  const [selectedAddress, setSelectedAddress] = useState<Address>(defaultAddress);
  const [selectedShipping, setSelectedShipping] = useState<ShippingOption>(shippingCouriers[0]);
  const [selectedPayment, setSelectedPayment] = useState<PaymentMethod>(paymentGateways[0]);
  const [vouchers, setVouchers] = useState<Voucher[]>(availableVouchers);
  const [buyerNote, setBuyerNote] = useState('');
  const [lastOrder, setLastOrder] = useState<OrderReceipt | null>(null);

  const cartCount = cart.reduce((acc, item) => acc + item.quantity, 0);

  const addToCart = (newItem: Omit<CartItem, 'id' | 'selected'>) => {
    setCart(prev => {
      const existingIdx = prev.findIndex(
        i => i.productId === newItem.productId && i.color === newItem.color && i.size === newItem.size
      );
      if (existingIdx > -1) {
        const updated = [...prev];
        updated[existingIdx].quantity += newItem.quantity;
        return updated;
      }
      const uniqueId = `cart-${Date.now()}-${Math.random().toString(36).substr(2, 4)}`;
      return [...prev, { ...newItem, id: uniqueId, selected: true }];
    });
    setIsCartOpen(true);
  };

  const removeFromCart = (id: string) => {
    setCart(prev => prev.filter(i => i.id !== id));
  };

  const updateQuantity = (id: string, qty: number) => {
    if (qty < 1) return;
    setCart(prev => prev.map(i => (i.id === id ? { ...i, quantity: qty } : i)));
  };

  const clearCart = () => {
    setCart([]);
  };

  const toggleVoucher = (code: string) => {
    setVouchers(prev =>
      prev.map(v => (v.code === code ? { ...v, applied: !v.applied } : v))
    );
  };

  const applyVoucherCode = (code: string): boolean => {
    const trimmed = code.trim().toUpperCase();
    const found = vouchers.find(v => v.code.toUpperCase() === trimmed);
    if (found) {
      setVouchers(prev =>
        prev.map(v => (v.code.toUpperCase() === trimmed ? { ...v, applied: true } : v))
      );
      return true;
    }
    return false;
  };

  const appliedVouchers = vouchers.filter(v => v.applied);

  // Financials
  const subtotal = cart.reduce((acc, item) => acc + item.price * item.quantity, 0);
  const shippingCost = selectedShipping.cost;

  const shippingDiscount = appliedVouchers
    .filter(v => v.type === 'shipping')
    .reduce((acc, v) => acc + Math.min(shippingCost, v.discount), 0);

  const productDiscount = appliedVouchers
    .filter(v => v.type === 'percentage' || v.type === 'fixed')
    .reduce((acc, v) => acc + v.discount, 0);

  const serviceFee = subtotal > 0 ? 1000 : 0;
  const grandTotal = Math.max(0, subtotal + shippingCost - shippingDiscount - productDiscount + serviceFee);

  const createOrder = (): OrderReceipt => {
    const year = new Date().getFullYear();
    const randDigits = Math.floor(100000 + Math.random() * 900000);
    const invoiceCode = `MLG-INV-${year}-${randDigits}`;
    const trackingCode = `SPXID0${Math.floor(1000000000 + Math.random() * 9000000000)}`;

    const order: OrderReceipt = {
      orderId: `ORD-${year}-${randDigits}`,
      invoiceNumber: invoiceCode,
      trackingNumber: trackingCode,
      items: [...cart],
      address: selectedAddress,
      shipping: selectedShipping,
      payment: selectedPayment,
      subtotal,
      shippingCost,
      shippingDiscount,
      productDiscount,
      serviceFee,
      total: grandTotal,
      createdAt: new Date().toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }),
      status: selectedPayment.category === 'cod' ? 'Sedang Dikemas Penjual' : 'Payment Pending',
      buyerNote: buyerNote || 'Harap dicek sebelum kirim, terima kasih!'
    };

    setLastOrder(order);
    clearCart();
    return order;
  };

  return (
    <CartContext.Provider
      value={{
        cart,
        cartCount,
        isCartOpen,
        setIsCartOpen,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        selectedAddress,
        setSelectedAddress,
        selectedShipping,
        setSelectedShipping,
        selectedPayment,
        setSelectedPayment,
        vouchers,
        appliedVouchers,
        toggleVoucher,
        applyVoucherCode,
        buyerNote,
        setBuyerNote,
        subtotal,
        shippingCost,
        shippingDiscount,
        productDiscount,
        serviceFee,
        grandTotal,
        lastOrder,
        createOrder
      }}
    >
      {children}
    </CartContext.Provider>
  );
}

export function useCart() {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
}
