'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { CartItem, Voucher, Address, ShippingOption, PaymentMethod, OrderReceipt, Product } from '../types';
import { productsCatalog, availableVouchers, defaultAddress, shippingCouriers, paymentGateways } from '../data/products';

interface CartContextType {
  cart: CartItem[];
  cartCount: number;
  selectedItems: CartItem[];
  selectedCount: number;
  allSelected: boolean;
  isIndeterminate: boolean;
  isCartOpen: boolean;
  setIsCartOpen: (open: boolean) => void;
  
  // Cart Actions
  addToCart: (item: Omit<CartItem, 'id' | 'selected'>, openDrawer?: boolean) => void;
  instantBuy: (item: Omit<CartItem, 'id' | 'selected'>) => void;
  removeFromCart: (id: string) => void;
  toggleSelectItem: (id: string) => void;
  selectAllItems: (select: boolean) => void;
  removeSelectedItems: () => void;
  updateQuantity: (id: string, qty: number) => void;
  updateCartItemVariant: (id: string, color: string, size: string, price: number, originalPrice: number, image: string) => void;
  clearCart: () => void;
  clearPurchasedItems: () => void;
  
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
  const [cart, setCart] = useState<CartItem[]>([]);
  const [isLoaded, setIsLoaded] = useState(false);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [selectedAddress, setSelectedAddress] = useState<Address>(defaultAddress);
  const [selectedShipping, setSelectedShipping] = useState<ShippingOption>(shippingCouriers[0]);
  const [selectedPayment, setSelectedPayment] = useState<PaymentMethod>(paymentGateways[0]);
  const [vouchers, setVouchers] = useState<Voucher[]>(availableVouchers);
  const [buyerNote, setBuyerNote] = useState('');
  const [lastOrder, setLastOrder] = useState<OrderReceipt | null>(null);

  // 1. Load cart from localStorage cache on initial mount
  useEffect(() => {
    try {
      const saved = localStorage.getItem('malega_cart');
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed)) {
          // Ensure all restored items have valid 'selected' boolean defaulting to true if undefined
          const sanitized = parsed.map((item: any) => ({
            ...item,
            selected: item.selected !== false
          }));
          setCart(sanitized);
        }
      }
    } catch (err) {
      console.error('Failed to restore cart from cache:', err);
    } finally {
      setIsLoaded(true);
    }
  }, []);

  // 2. Persist or clear cart cache in localStorage on change
  useEffect(() => {
    if (!isLoaded) return;
    try {
      if (cart.length > 0) {
        localStorage.setItem('malega_cart', JSON.stringify(cart));
      } else {
        localStorage.removeItem('malega_cart');
      }
    } catch (err) {
      console.error('Failed to sync cart cache to localStorage:', err);
    }
  }, [cart, isLoaded]);

  const cartCount = cart.reduce((acc, item) => acc + item.quantity, 0);
  const selectedItems = cart.filter(item => item.selected);
  const selectedCount = selectedItems.reduce((acc, item) => acc + item.quantity, 0);
  const allSelected = cart.length > 0 && cart.every(item => item.selected);
  const isIndeterminate = cart.some(item => item.selected) && !allSelected;

  const addToCart = (newItem: Omit<CartItem, 'id' | 'selected'>, openDrawer: boolean = false) => {
    setCart(prev => {
      const existingIdx = prev.findIndex(
        i => i.productId === newItem.productId && i.color === newItem.color && i.size === newItem.size
      );
      if (existingIdx > -1) {
        return prev.map((item, idx) =>
          idx === existingIdx
            ? { ...item, quantity: item.quantity + newItem.quantity, selected: true }
            : item
        );
      }
      const uniqueId = `cart-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`;
      return [...prev, { ...newItem, id: uniqueId, selected: true }];
    });
    if (openDrawer) {
      setIsCartOpen(true);
    }
  };

  const instantBuy = (newItem: Omit<CartItem, 'id' | 'selected'>) => {
    setCart(prev => {
      // Unselect all other existing items in bag
      const unselectedPrev = prev.map(i => ({ ...i, selected: false }));
      const existingIdx = unselectedPrev.findIndex(
        i => i.productId === newItem.productId && i.color === newItem.color && i.size === newItem.size
      );
      if (existingIdx > -1) {
        return unselectedPrev.map((item, idx) =>
          idx === existingIdx
            ? { ...item, quantity: newItem.quantity, price: newItem.price, originalPrice: newItem.originalPrice, selected: true }
            : item
        );
      }
      const uniqueId = `cart-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`;
      return [...unselectedPrev, { ...newItem, id: uniqueId, selected: true }];
    });
  };

  const toggleSelectItem = (id: string) => {
    setCart(prev => prev.map(i => (i.id === id ? { ...i, selected: !i.selected } : i)));
  };

  const selectAllItems = (select: boolean) => {
    setCart(prev => prev.map(i => ({ ...i, selected: select })));
  };

  const removeSelectedItems = () => {
    setCart(prev => {
      const remaining = prev.filter(i => !i.selected);
      if (remaining.length === 0 && typeof window !== 'undefined') {
        localStorage.removeItem('malega_cart');
      }
      return remaining;
    });
  };

  const removeFromCart = (id: string) => {
    setCart(prev => {
      const next = prev.filter(i => i.id !== id);
      if (next.length === 0 && typeof window !== 'undefined') {
        localStorage.removeItem('malega_cart');
      }
      return next;
    });
  };

  const updateQuantity = (id: string, qty: number) => {
    if (qty < 1) return;
    setCart(prev => prev.map(i => (i.id === id ? { ...i, quantity: qty } : i)));
  };

  const updateCartItemVariant = (id: string, color: string, size: string, price: number, originalPrice: number, image: string) => {
    setCart(prev => {
      const targetItem = prev.find(i => i.id === id);
      if (!targetItem) return prev;

      const existingDuplicate = prev.find(
        i => i.id !== id && i.productId === targetItem.productId && i.color === color && i.size === size
      );

      if (existingDuplicate) {
        return prev
          .map(i => i.id === existingDuplicate.id ? { ...i, quantity: i.quantity + targetItem.quantity, selected: true } : i)
          .filter(i => i.id !== id);
      }

      return prev.map(i => i.id === id ? { ...i, color, size, price, originalPrice, image } : i);
    });
  };

  const clearCart = () => {
    setCart([]);
    if (typeof window !== 'undefined') {
      localStorage.removeItem('malega_cart');
    }
  };

  const clearPurchasedItems = () => {
    setCart(prev => {
      const remaining = prev.filter(i => !i.selected);
      if (remaining.length === 0 && typeof window !== 'undefined') {
        localStorage.removeItem('malega_cart');
      }
      return remaining;
    });
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

  // Financials strictly based on selectedItems
  const subtotal = selectedItems.reduce((acc, item) => acc + item.price * item.quantity, 0);
  const shippingCost = selectedItems.length > 0 ? selectedShipping.cost : 0;

  const shippingDiscount = selectedItems.length > 0
    ? appliedVouchers
        .filter(v => v.type === 'shipping')
        .reduce((acc, v) => acc + Math.min(shippingCost, v.discount), 0)
    : 0;

  const productDiscount = selectedItems.length > 0
    ? appliedVouchers
        .filter(v => v.type === 'percentage' || v.type === 'fixed')
        .reduce((acc, v) => acc + v.discount, 0)
    : 0;

  const serviceFee = subtotal > 0 ? 1000 : 0;
  const grandTotal = Math.max(0, subtotal + shippingCost - shippingDiscount - productDiscount + serviceFee);

  const createOrder = (): OrderReceipt => {
    const year = new Date().getFullYear();
    const randDigits = Math.floor(100000 + Math.random() * 900000);
    const invoiceCode = `MLG-INV-${year}-${randDigits}`;
    const trackingCode = `SPXID0${Math.floor(1000000000 + Math.random() * 9000000000)}`;

    const orderItems = [...selectedItems];

    const order: OrderReceipt = {
      orderId: `ORD-${year}-${randDigits}`,
      invoiceNumber: invoiceCode,
      trackingNumber: trackingCode,
      items: orderItems,
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
    clearPurchasedItems();
    return order;
  };

  return (
    <CartContext.Provider
      value={{
        cart,
        cartCount,
        selectedItems,
        selectedCount,
        allSelected,
        isIndeterminate,
        isCartOpen,
        setIsCartOpen,
        addToCart,
        instantBuy,
        removeFromCart,
        toggleSelectItem,
        selectAllItems,
        removeSelectedItems,
        updateQuantity,
        updateCartItemVariant,
        clearCart,
        clearPurchasedItems,
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
