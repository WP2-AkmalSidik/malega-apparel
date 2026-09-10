'use client';

import { useState, useEffect, useCallback } from 'react';
import { useAuth } from '../../../context/AuthContext';
import { CustomerPastOrder, LiveTrackingOrder } from '../../../types';
import { formatRupiah } from '../../../lib/utils';
import { getMilestones } from '../../track/_lib/milestones';

export function useAccount() {
  const { customer, isAuthenticated, token, logout, updateProfile } = useAuth();

  const [activeTab, setActiveTab] = useState<
    'orders' | 'tracking' | 'addresses' | 'settings'
  >('orders');
  const [orders, setOrders] = useState<CustomerPastOrder[]>([]);
  const [isLoadingOrders, setIsLoadingOrders] = useState<boolean>(false);

  // Tracking Dashboard State
  const [selectedTrackingOrderNumber, setSelectedTrackingOrderNumber] = useState<string | null>(null);
  const [trackingOrder, setTrackingOrder] = useState<LiveTrackingOrder | null>(null);
  const [isLoadingTracking, setIsLoadingTracking] = useState<boolean>(false);
  const [trackingError, setTrackingError] = useState<string | null>(null);
  const [activeTrackingSubTab, setActiveTrackingSubTab] = useState<'timeline' | 'package' | 'invoice' | 'address'>('timeline');
  const [copiedKey, setCopiedKey] = useState<string | null>(null);
  const [isGeneratingInvoice, setIsGeneratingInvoice] = useState<boolean>(false);
  const [toast, setToast] = useState<{ title: string; subtitle?: string } | null>(null);

  // New Address State
  const [showAddressModal, setShowAddressModal] = useState(false);
  const [addrName, setAddrName] = useState('');
  const [addrPhone, setAddrPhone] = useState('');
  const [addrStreet, setAddrStreet] = useState('');
  const [addrDistrict, setAddrDistrict] = useState('');
  const [addrCity, setAddrCity] = useState('');
  const [addrProvince, setAddrProvince] = useState('');
  const [addrPostal, setAddrPostal] = useState('');

  const API_BASE =
    process.env.NEXT_PUBLIC_BACKEND_API_URL ||
    process.env.NEXT_PUBLIC_API_URL ||
    'https://malega.my.id/api/v1';

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
    try {
      const res = await fetch(`${API_BASE}/payments/invoice`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
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

  // Fetch full live tracking details for an order
  const fetchTrackingDetail = useCallback(
    async (orderNumber: string) => {
      if (!orderNumber) return;
      setIsLoadingTracking(true);
      setTrackingError(null);

      try {
        const res = await fetch(
          `${API_BASE}/orders/${encodeURIComponent(orderNumber.trim())}`,
          {
            cache: 'no-store',
            headers: {
              ...(token ? { Authorization: `Bearer ${token}` } : {}),
            },
          }
        );

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
              shippingAddress: d.shipping_address || {
                recipient_name: customer?.name || '-',
                phone: customer?.phone || '-',
                address_line1: '-',
                city: '-',
                province: '-',
                postal_code: '-',
              },
              shipment: d.shipment || null,
              payment: d.payment || null,
              items: d.items || [],
            };
            setTrackingOrder(liveOrder);
            setIsLoadingTracking(false);
            return;
          }
        }

        // Fallback: construct from loaded orders list if endpoint returns 404 or cache miss
        const found = orders.find((o) => o.order_number === orderNumber);
        if (found) {
          const fallbackLive: LiveTrackingOrder = {
            orderNumber: found.order_number,
            createdAt: found.created_at,
            orderStatus: { code: found.status, label: found.status_label },
            paymentStatus: {
              code: found.payment?.status || (['paid', 'completed', 'delivered'].includes(found.status) ? 'paid' : 'unpaid'),
              label: (found.payment?.status === 'PAID' || ['paid', 'completed', 'delivered'].includes(found.status)) ? 'Lunas (Paid)' : 'Menunggu Pembayaran',
            },
            fulfillmentStatus: {
              code: found.status === 'shipped' ? 'shipped' : found.status === 'delivered' ? 'delivered' : 'unfulfilled',
              label: found.status_label,
            },
            pricing: {
              subtotal: found.total_amount,
              discount_total: 0,
              shipping_total: 0,
              service_fee: 0,
              tax_total: 0,
              grand_total: found.total_amount,
              formatted_grand_total: found.formatted_total,
            },
            customer: {
              name: customer?.name || 'Pelanggan Malega',
              email: customer?.email,
              phone: customer?.phone,
            },
            shippingAddress: {
              recipient_name: found.address?.recipient_name || customer?.name || '-',
              phone: found.address?.phone || customer?.phone || '-',
              address_line1: found.address?.address_line1 || '-',
              address_line2: found.address?.address_line2,
              city: found.address?.city || '-',
              province: found.address?.province || '-',
              postal_code: found.address?.postal_code || '-',
              courier_name: found.shipping?.courier,
              tracking_number: found.shipping?.waybill,
            },
            shipment: found.shipping
              ? {
                  courier: found.shipping.courier,
                  service: found.shipping.service || 'Reguler',
                  waybill_id: found.shipping.waybill,
                  status: found.shipping.status || found.status,
                  status_label: found.shipping.status_label || found.status_label,
                  tracking_url: found.shipping.tracking_url,
                  tracking_history: found.shipping.tracking_history || [],
                }
              : null,
            payment: found.payment
              ? {
                  payment_method: found.payment.method,
                  payment_method_name: found.payment.method_name || found.payment.method,
                  status: found.payment.status,
                  payment_url: found.payment.payment_url,
                  reference: found.payment.reference,
                  paid_at: found.payment.paid_at,
                }
              : null,
            items: found.items.map((it) => ({
              sku: it.sku,
              product_name: it.product_name || it.title,
              variant_title: it.title,
              unit_price: it.price,
              formatted_unit_price: formatRupiah(it.price),
              quantity: it.quantity,
              subtotal: it.subtotal,
              formatted_subtotal: formatRupiah(it.subtotal),
            })),
          };
          setTrackingOrder(fallbackLive);
        } else {
          setTrackingError('Data pelacakan pesanan tidak ditemukan.');
          setTrackingOrder(null);
        }
      } catch (err) {
        console.error('Error loading live tracking detail:', err);
        setTrackingError('Gagal memuat status logistik real-time.');
      } finally {
        setIsLoadingTracking(false);
      }
    },
    [API_BASE, token, customer, orders]
  );

  // 1-Click action to track a specific order from history or selector
  const selectOrderForTracking = useCallback(
    (orderNumber: string) => {
      setSelectedTrackingOrderNumber(orderNumber);
      setActiveTab('tracking');
      fetchTrackingDetail(orderNumber);
      if (typeof window !== 'undefined') {
        window.scrollTo({ top: 120, behavior: 'smooth' });
      }
    },
    [fetchTrackingDetail]
  );

  // Fetch customer past orders on mount
  useEffect(() => {
    if (isAuthenticated && token) {
      setIsLoadingOrders(true);
      fetch(`${API_BASE}/customers/orders`, {
        headers: { Authorization: `Bearer ${token}` },
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success && Array.isArray(data.data)) {
            setOrders(data.data);
            // Default selected tracking order to the first / most recent order
            if (data.data.length > 0 && !selectedTrackingOrderNumber) {
              const latestOrderNum = data.data[0].order_number;
              setSelectedTrackingOrderNumber(latestOrderNum);
            }
          }
        })
        .catch((err) => console.error('Error fetching customer orders:', err))
        .finally(() => setIsLoadingOrders(false));
    }
  }, [isAuthenticated, token, API_BASE]);

  // Load tracking detail when selected order number changes or tab is tracking
  useEffect(() => {
    if (activeTab === 'tracking' && selectedTrackingOrderNumber && !trackingOrder) {
      fetchTrackingDetail(selectedTrackingOrderNumber);
    }
  }, [activeTab, selectedTrackingOrderNumber, trackingOrder, fetchTrackingDetail]);

  const handleAddAddress = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!customer) return;

    const newAddr = {
      name: addrName || customer.name,
      phone: addrPhone || customer.phone,
      street: addrStreet,
      district: addrDistrict,
      city: addrCity,
      province: addrProvince,
      postalCode: addrPostal,
      isDefault: (customer.saved_addresses?.length || 0) === 0,
    };

    const updatedAddresses = [...(customer.saved_addresses || []), newAddr];
    await updateProfile({ saved_addresses: updatedAddresses });
    setShowAddressModal(false);
    setAddrStreet('');
    setAddrDistrict('');
    setAddrCity('');
    setAddrProvince('');
    setAddrPostal('');
  };

  // Compute progress step index (1-5)
  const getProgressStep = () => {
    if (!trackingOrder) return 1;
    const s = trackingOrder.shipment?.status?.toLowerCase() || '';
    if (trackingOrder.fulfillmentStatus?.code === 'delivered' || s === 'delivered') return 5;
    if (['in_transit', 'dropping_off', 'shipped'].includes(s)) return 4;
    if (
      ['picking_up', 'picked', 'allocated', 'confirmed'].includes(s) ||
      trackingOrder.shipment?.waybill_id
    )
      return 3;
    if (
      trackingOrder.paymentStatus?.code === 'paid' ||
      trackingOrder.orderStatus?.code === 'processing'
    )
      return 2;
    return 1;
  };

  const progressStep = getProgressStep();
  const milestones = trackingOrder ? getMilestones(trackingOrder, progressStep) : [];
  const courierCompany =
    trackingOrder?.shipment?.courier ||
    trackingOrder?.shippingAddress?.courier_name ||
    'JNE Express (Reguler)';

  const waText = trackingOrder
    ? encodeURIComponent(
        `Halo Concierge Malega Apparel, saya ingin menanyakan status pesanan saya:\n\n` +
          `*No. Pesanan:* ${trackingOrder.orderNumber || '-'}\n` +
          `*Penerima:* ${trackingOrder.shippingAddress?.recipient_name || '-'}\n` +
          `*Ekspedisi:* ${courierCompany}\n\n` +
          `Mohon dibantu informasinya ya min, terima kasih!`
      )
    : '';

  return {
    customer,
    isAuthenticated,
    logout,
    updateProfile,
    activeTab,
    setActiveTab,
    orders,
    isLoadingOrders,

    // Tracking specifics
    selectedTrackingOrderNumber,
    setSelectedTrackingOrderNumber,
    trackingOrder,
    isLoadingTracking,
    trackingError,
    activeTrackingSubTab,
    setActiveTrackingSubTab,
    copiedKey,
    isGeneratingInvoice,
    toast,
    progressStep,
    milestones,
    courierCompany,
    waText,
    copyToClipboard,
    handleCreatePaymentInvoice,
    fetchTrackingDetail,
    selectOrderForTracking,

    // Address Book specifics
    showAddressModal,
    setShowAddressModal,
    addrName,
    setAddrName,
    addrPhone,
    setAddrPhone,
    addrStreet,
    setAddrStreet,
    addrDistrict,
    setAddrDistrict,
    addrCity,
    setAddrCity,
    addrProvince,
    setAddrProvince,
    addrPostal,
    setAddrPostal,
    handleAddAddress,
  };
}

