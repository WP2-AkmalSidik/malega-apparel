'use client';

import { useState, useEffect } from 'react';
import { useSearchParams } from 'next/navigation';
import { useCart } from '../../../context/CartContext';
import { products } from '../../../data/products';
import { formatRupiah } from '../../../lib/utils';
import { mockOrderConfirmation } from '../_constants/mock-order';
import { LuxuryToastData } from '../../../components/LuxuryToast';

export function useOrderConfirmation() {
  const searchParams = useSearchParams();
  const orderNumberParam =
    searchParams.get('order_number') ||
    searchParams.get('merchantOrderId') ||
    searchParams.get('orderId');
  const { lastOrder } = useCart();

  const [liveOrder, setLiveOrder] = useState<any>(null);
  const [isLoadingOrder, setIsLoadingOrder] = useState<boolean>(false);
  const [toast, setToast] = useState<LuxuryToastData | null>(null);
  const [copiedInvoice, setCopiedInvoice] = useState(false);
  const [copiedResi, setCopiedResi] = useState(false);

  const copyText = (text: string, type: 'invoice' | 'resi') => {
    if (!text || text === '-' || text.includes('Menunggu') || text.includes('Sedang Diproses')) {
      setToast({
        title: 'Nomor Resi Belum Diterbitkan',
        subtitle: 'Paket sedang disiapkan penjual. Gunakan Nomor Invoice untuk melacak status pesanan.',
      });
      setTimeout(() => setToast(null), 3000);
      return;
    }

    navigator.clipboard.writeText(text);

    if (type === 'invoice') {
      setCopiedInvoice(true);
      setTimeout(() => setCopiedInvoice(false), 2000);
      setToast({
        title: 'Nomor Invoice Berhasil Disalin',
        subtitle: text,
      });
    } else {
      setCopiedResi(true);
      setTimeout(() => setCopiedResi(false), 2000);
      setToast({
        title: 'Nomor Resi Berhasil Disalin',
        subtitle: text,
      });
    }

    setTimeout(() => setToast(null), 2500);
  };

  useEffect(() => {
    const currentOrderParam = orderNumberParam;
    if (!currentOrderParam) return;

    let isMounted = true;
    async function fetchLiveOrder() {
      setIsLoadingOrder(true);
      try {
        const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'https://malega.my.id/api/v1';
        const orderCode = String(currentOrderParam);
        const res = await fetch(`${apiUrl}/orders/${encodeURIComponent(orderCode)}`);
        const json = await res.json();
        if (isMounted && json.success && json.data) {
          const data = json.data;
          const actualTrackingNum =
            data.shipment?.waybill_id || data.shipping_address?.tracking_number || null;
          const invoiceNum = data.order_number || currentOrderParam;

          // Save session to localStorage for seamless auto-tracking
          try {
            localStorage.setItem(
              'malega_last_order',
              JSON.stringify({
                orderNumber: invoiceNum,
                trackingNumber: actualTrackingNum,
                grandTotal: data.pricing?.grand_total || data.grand_total,
                createdAt: data.created_at || new Date().toISOString(),
              })
            );
            localStorage.setItem('malega_last_order_number', invoiceNum);
            if (actualTrackingNum) {
              localStorage.setItem('malega_last_tracking_number', actualTrackingNum);
            }
          } catch (e) {
            console.warn('Storage write error:', e);
          }

          setLiveOrder({
            orderId: invoiceNum,
            invoiceNumber: invoiceNum,
            trackingNumber: actualTrackingNum || 'Sedang Diproses Penjual',
            hasActualResi: Boolean(actualTrackingNum),
            items: (data.items || []).map((item: any) => {
              const catalogMatch = products.find(
                (p) =>
                  (p.title &&
                    item.product_name &&
                    p.title.toLowerCase().includes(item.product_name.toLowerCase())) ||
                  (p.title &&
                    item.product_name &&
                    item.product_name.toLowerCase().includes(p.title.toLowerCase())) ||
                  (p.slug && item.sku && item.sku.toLowerCase().includes(p.slug.toLowerCase()))
              );
              const colorParsed = item.variant_title?.split('/')[0]?.trim() || 'Signature';
              const sizeParsed = item.variant_title?.split('/')[1]?.trim() || 'All Size';
              const fallbackImg =
                catalogMatch?.gallery?.[0] ||
                catalogMatch?.colors?.[0]?.image ||
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80';
              return {
                id: item.id || item.sku,
                title: item.product_name,
                color: colorParsed,
                size: sizeParsed,
                price: item.unit_price,
                quantity: item.quantity,
                image: item.image_url || fallbackImg,
              };
            }),
            address: {
              name: data.customer?.name || data.shipping_address?.recipient_name || 'Pelanggan Malega',
              phone: data.customer?.phone || data.shipping_address?.phone || '081234567890',
              street: data.shipping_address?.address_line1 || 'Jl. Kemang Raya',
              city: data.shipping_address?.city || 'Jakarta Selatan',
              postalCode: data.shipping_address?.postal_code || '12730',
            },
            shipping: {
              name:
                data.shipping_address?.courier_name ||
                data.shipment?.courier ||
                'Biteship Logistics',
              courier:
                data.shipment?.courier ||
                data.shipping_address?.courier_name ||
                'Biteship Express',
              cost: data.pricing?.shipping_total ?? data.shipping_total ?? 15000,
              etd: '1 - 2 Hari Kerja',
            },
            payment: {
              name: data.payment?.payment_method_name || 'Duitku Payment Gateway',
              category: 'duitku',
              status: data.payment_status?.label || 'Lunas',
            },
            subtotal: data.pricing?.subtotal ?? data.subtotal ?? 0,
            shippingCost: data.pricing?.shipping_total ?? data.shipping_total ?? 0,
            shippingDiscount:
              data.pricing?.shipping_total &&
              data.pricing?.shipping_total > 0 &&
              data.pricing?.grand_total <
                data.pricing?.subtotal + data.pricing?.shipping_total
                ? 15000
                : 0,
            productDiscount: data.pricing?.discount_total ?? data.discount_total ?? 0,
            serviceFee: data.pricing?.service_fee ?? 1000,
            total: data.pricing?.grand_total ?? data.grand_total ?? 0,
            createdAt: data.created_at
              ? new Date(data.created_at).toLocaleString('id-ID', {
                  day: 'numeric',
                  month: 'short',
                  year: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit',
                })
              : new Date().toLocaleString('id-ID'),
            status: data.order_status?.label || 'Sedang Diproses',
          });
        }
      } catch (err) {
        console.warn('Failed to fetch live order tracking confirmation:', err);
      } finally {
        if (isMounted) setIsLoadingOrder(false);
      }
    }

    fetchLiveOrder();

    return () => {
      isMounted = false;
    };
  }, [orderNumberParam]);

  // Order display hierarchy: Live API Order -> Cart Last Order -> Mock Demo Order
  const order =
    liveOrder ||
    (lastOrder
      ? {
          ...lastOrder,
          hasActualResi: Boolean(
            lastOrder.trackingNumber && !lastOrder.trackingNumber.includes('undefined')
          ),
        }
      : {
          ...mockOrderConfirmation,
          createdAt: new Date().toLocaleString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
          }),
        });

  const waText = encodeURIComponent(
    `Halo Admin Malega Apparel, saya baru saja melakukan pemesanan di Website Resmi:\n\n*No. Invoice:* ${order.invoiceNumber}\n*No. Resi:* ${order.trackingNumber}\n*Nama Penerima:* ${order.address.name} (${order.address.phone})\n*Total Pembayaran:* ${formatRupiah(order.total)}\n*Metode Pembayaran:* ${order.payment.name}\n\nMohon bantu verifikasi dan proses pengirimannya ya min, terima kasih!`
  );

  return {
    order,
    isLoadingOrder,
    toast,
    copiedInvoice,
    copiedResi,
    copyText,
    waText,
  };
}
