'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '../../../context/AuthContext';
import { CustomerPastOrder } from '../../../types';

export function useAccount() {
  const { customer, isAuthenticated, token, logout, updateProfile } = useAuth();

  const [activeTab, setActiveTab] = useState<
    'orders' | 'addresses' | 'wishlist' | 'settings'
  >('orders');
  const [orders, setOrders] = useState<CustomerPastOrder[]>([]);
  const [isLoadingOrders, setIsLoadingOrders] = useState<boolean>(false);

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
    process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

  // Fetch orders when logged in
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
          }
        })
        .catch((err) => console.error('Error fetching customer orders:', err))
        .finally(() => setIsLoadingOrders(false));
    }
  }, [isAuthenticated, token, API_BASE]);

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

  return {
    customer,
    isAuthenticated,
    logout,
    updateProfile,
    activeTab,
    setActiveTab,
    orders,
    isLoadingOrders,
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
