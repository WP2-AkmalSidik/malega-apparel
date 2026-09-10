'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { CartItem, ShippingOption, PaymentMethod, Voucher, CustomerProfile } from '../../../types';

interface UseCheckoutSubmitOptions {
  checkoutItems: CartItem[];
  selectedAddress: any;
  selectedShipping: ShippingOption;
  selectedPayment: PaymentMethod;
  appliedVouchers: Voucher[];
  shippingCost: number;
  serviceFee: number;
  productDiscount: number;
  shippingDiscount: number;
  grandTotal: number;
  buyerNote: string;
  createOrder: () => void;
  customer?: CustomerProfile | null;
  token?: string | null;
  isAuthenticated?: boolean;
}

export function useCheckoutSubmit({
  checkoutItems,
  selectedAddress,
  selectedShipping,
  selectedPayment,
  appliedVouchers,
  shippingCost,
  serviceFee,
  productDiscount,
  shippingDiscount,
  grandTotal,
  buyerNote,
  createOrder,
  customer,
  token,
  isAuthenticated,
}: UseCheckoutSubmitOptions) {
  const router = useRouter();
  const [isProcessing, setIsProcessing] = useState(false);
  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [livePaymentResult, setLivePaymentResult] = useState<{
    payment_url?: string | null;
    reference?: string | null;
    va_number?: string | null;
    qr_string?: string | null;
  } | null>(null);

  const handleProceedPayment = async () => {
    setIsProcessing(true);
    try {
      const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api/v1';

      const paymentMethodCode =
        selectedPayment.duitkuCode ||
        (selectedPayment.category === 'qris'
          ? 'SP'
          : selectedPayment.category === 'card'
          ? 'VC'
          : 'BC');

      const appliedVoucherCode = appliedVouchers.length > 0 ? appliedVouchers[0].code : null;

      const customerEmail =
        customer?.email ||
        selectedAddress.email ||
        'pelanggan@malega.my.id';

      const customerName =
        selectedAddress.name ||
        customer?.name ||
        'Pelanggan Malega';

      const customerPhone =
        selectedAddress.phone ||
        customer?.phone ||
        '081234567890';

      const payload = {
        customer: {
          name: customerName,
          email: customerEmail,
          phone: customerPhone,
        },
        shipping_address: {
          recipient_name: customerName,
          phone: customerPhone,
          address_line1: selectedAddress.street || 'Jl. Malega No. 1',
          address_line2: selectedAddress.district || '',
          city: selectedAddress.city || 'Jakarta Selatan',
          province: selectedAddress.province || 'DKI Jakarta',
          postal_code: selectedAddress.postalCode || '12730',
          courier_name: `${selectedShipping.courier} (${selectedShipping.service})`,
        },
        items: checkoutItems.map((item) => ({
          variant_id: item.variantId || item.id,
          sku: item.sku || item.slug,
          product_name: item.title,
          variant_title: `${item.color} / ${item.size}`,
          unit_price: item.price,
          quantity: item.quantity,
        })),
        payment_method: paymentMethodCode,
        voucher_code: appliedVoucherCode,
        voucher_codes: appliedVouchers.map((v) => v.code),
        shipping_total: shippingCost,
        service_fee: serviceFee,
        discount_total: productDiscount + shippingDiscount,
        notes: buyerNote || 'Pesanan dari Storefront Malega',
        ...(customer?.id ? { authenticated_customer_id: customer.id } : {}),
      };

      const res = await fetch(`${apiUrl}/orders/checkout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json();

      if (data.success) {
        try {
          localStorage.setItem(
            'malega_last_order',
            JSON.stringify({
              orderNumber: data.data?.order_number,
              grandTotal: data.data?.pricing?.grand_total,
              paymentUrl: data.payment?.payment_url,
              createdAt: new Date().toISOString(),
            })
          );
        } catch (e) {
          console.warn('Could not save pending order to localStorage:', e);
        }

        createOrder();

        if (data.payment?.payment_url) {
          window.location.href = data.payment.payment_url;
          return;
        }

        setLivePaymentResult(data.payment || null);
        setIsProcessing(false);
        setShowPaymentModal(true);
        return;
      }

      setIsProcessing(false);
      setShowPaymentModal(true);
    } catch (err) {
      console.error('Checkout API error:', err);
      setIsProcessing(false);
      setShowPaymentModal(true);
    }
  };

  const handleConfirmOrderFinal = () => {
    setShowPaymentModal(false);
    createOrder();
    router.push('/order-confirmation');
  };

  return {
    isProcessing,
    showPaymentModal,
    setShowPaymentModal,
    livePaymentResult,
    handleProceedPayment,
    handleConfirmOrderFinal,
  };
}
