'use client';

import { useState, useEffect } from 'react';
import { LiveTrackingOrder } from '../../../types';
import { formatRupiah } from '../../../lib/utils';
import { createFallbackOrder } from '../_constants/fallback-data';
import { getMilestones } from '../_lib/milestones';

interface UseTrackingOptions {
  initialQuery: string;
}

export function useTracking({ initialQuery }: UseTrackingOptions) {
  const [searchQuery, setSearchQuery] = useState(initialQuery);
  const [activeTab, setActiveTab] = useState<'timeline' | 'package' | 'invoice'>('timeline');
  const [isLoading, setIsLoading] = useState(false);
  const [order, setOrder] = useState<LiveTrackingOrder | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [copiedKey, setCopiedKey] = useState<string | null>(null);
  const [isGeneratingInvoice, setIsGeneratingInvoice] = useState(false);
  const [toast, setToast] = useState<{ title: string; subtitle?: string } | null>(null);

  const copyToClipboard = (text: string, key: string, label: string = 'Nomor') => {
    if (!text || text === '-') return;
    navigator.clipboard.writeText(text);
    setCopiedKey(key);
    setToast({
      title: `${label} Berhasil Disalin`,
      subtitle: text,
    });
    setTimeout(() => setCopiedKey(null), 2000);
    setTimeout(() => setToast(null), 2500);
  };

  const handleCreatePaymentInvoice = async (orderNumber: string) => {
    setIsGeneratingInvoice(true);
    const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'https://malega.my.id/api/v1';
    try {
      const res = await fetch(`${apiUrl}/payments/invoice`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({
          order_number: orderNumber,
          payment_method: 'SP',
        }),
      });
      const data = await res.json();
      if (data.success && data.data?.payment_url) {
        window.location.href = data.data.payment_url;
        return;
      }
      setIsGeneratingInvoice(false);
    } catch (err) {
      console.error('Failed to create payment invoice:', err);
      setIsGeneratingInvoice(false);
    }
  };

  const fetchTracking = async (term: string) => {
    if (!term.trim()) return;

    setIsLoading(true);
    setError(null);

    const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'https://malega.my.id/api/v1';

    try {
      const res = await fetch(`${apiUrl}/orders/${encodeURIComponent(term.trim())}`, {
        cache: 'no-store',
      });

      if (res.ok) {
        const json = await res.json();
        if (json.success && json.data) {
          const d = json.data;
          const liveOrder: LiveTrackingOrder = {
            orderNumber: d.order_number,
            createdAt: d.created_at,
            orderStatus: d.order_status,
            paymentStatus: d.payment_status,
            fulfillmentStatus: d.fulfillment_status,
            pricing: {
              subtotal: d.pricing?.subtotal || 0,
              discount_total: d.pricing?.discount_total || 0,
              shipping_total: d.pricing?.shipping_total || 0,
              service_fee: d.pricing?.service_fee ?? 0,
              tax_total: d.pricing?.tax_total || 0,
              grand_total: d.pricing?.grand_total || 0,
              formatted_grand_total:
                d.pricing?.formatted_grand_total || formatRupiah(d.pricing?.grand_total || 0),
            },
            customer: d.customer || { name: d.shipping_address?.recipient_name },
            shippingAddress: d.shipping_address,
            shipment: d.shipment || null,
            payment: d.payment || null,
            items: d.items || [],
          };

          // Save active session for instant auto-populate
          try {
            localStorage.setItem(
              'malega_last_order',
              JSON.stringify({
                orderNumber: liveOrder.orderNumber,
                trackingNumber:
                  liveOrder.shippingAddress?.tracking_number || liveOrder.shipment?.waybill_id,
                grandTotal: liveOrder.pricing?.grand_total,
                createdAt: liveOrder.createdAt,
              })
            );
            localStorage.setItem('malega_last_order_number', liveOrder.orderNumber);
            if (liveOrder.shippingAddress?.tracking_number || liveOrder.shipment?.waybill_id) {
              localStorage.setItem(
                'malega_last_tracking_number',
                liveOrder.shippingAddress?.tracking_number || liveOrder.shipment?.waybill_id || ''
              );
            }
          } catch (e) {
            // ignore
          }

          setOrder(liveOrder);
          setIsLoading(false);
          return;
        }
      }

      // If search query looks like a test identifier but not found in DB
      if (term.includes('MLG-') || term.includes('WYB-') || term.includes('JNE-')) {
        setOrder(createFallbackOrder(term));
        setIsLoading(false);
        return;
      }

      setError(
        `Pesanan dengan nomor "${term}" tidak ditemukan. Silakan periksa kembali nomor pesanan pada email konfirmasi Anda.`
      );
      setOrder(null);
    } catch (err) {
      setError(
        'Gagal menghubungkan ke server logistik. Silakan periksa koneksi internet Anda atau coba beberapa saat lagi.'
      );
      setOrder(null);
    } finally {
      setIsLoading(false);
    }
  };

  // Auto-populate from URL params or localStorage
  useEffect(() => {
    if (initialQuery) {
      setSearchQuery(initialQuery);
      fetchTracking(initialQuery);
    } else {
      try {
        const lastOrderNum = localStorage.getItem('malega_last_order_number');
        const lastTrackingNum = localStorage.getItem('malega_last_tracking_number');
        const stored = localStorage.getItem('malega_last_order');
        let targetTerm = '';
        if (lastOrderNum) {
          targetTerm = lastOrderNum;
        } else if (lastTrackingNum) {
          targetTerm = lastTrackingNum;
        } else if (stored) {
          const parsed = JSON.parse(stored);
          targetTerm = parsed?.orderNumber || parsed?.trackingNumber || '';
        }

        if (targetTerm) {
          setSearchQuery(targetTerm);
          fetchTracking(targetTerm);
        }
      } catch (e) {
        // ignore
      }
    }
  }, [initialQuery]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (!searchQuery.trim()) return;
    fetchTracking(searchQuery);
  };

  // Compute progress step index (1-5)
  const getProgressStep = () => {
    if (!order) return 1;
    const s = order.shipment?.status?.toLowerCase() || '';
    if (order.fulfillmentStatus?.code === 'delivered' || s === 'delivered') return 5;
    if (['in_transit', 'dropping_off', 'shipped'].includes(s)) return 4;
    if (['picking_up', 'picked', 'allocated', 'confirmed'].includes(s) || order.shipment?.waybill_id)
      return 3;
    if (order.paymentStatus?.code === 'paid' || order.orderStatus?.code === 'processing') return 2;
    return 1;
  };

  const progressStep = getProgressStep();
  const milestones = order ? getMilestones(order, progressStep) : [];
  const courierCompany =
    order?.shipment?.courier || order?.shippingAddress?.courier_name || 'JNE (Reguler)';

  const waText = order
    ? encodeURIComponent(
        `Halo Concierge Malega Apparel, saya ingin menanyakan status pesanan saya:\n\n` +
          `*No. Pesanan:* ${order.orderNumber || '-'}\n` +
          `*Penerima:* ${order.shippingAddress?.recipient_name || '-'}\n` +
          `*Ekspedisi:* ${courierCompany}\n\n` +
          `Mohon dibantu informasinya ya min, terima kasih!`
      )
    : '';

  return {
    // State
    searchQuery,
    setSearchQuery,
    activeTab,
    setActiveTab,
    isLoading,
    order,
    error,
    copiedKey,
    isGeneratingInvoice,
    toast,
    setToast,
    progressStep,
    milestones,
    courierCompany,
    waText,

    // Actions
    copyToClipboard,
    handleCreatePaymentInvoice,
    fetchTracking,
    handleSearch,
  };
}
