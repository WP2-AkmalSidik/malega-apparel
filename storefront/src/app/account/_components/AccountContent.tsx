'use client';

import React from 'react';
import { Package, MapPin, Settings } from 'lucide-react';
import { useAccount } from '../_hooks/useAccount';
import AuthGateway from './AuthGateway';
import MemberHeader from './MemberHeader';
import OrderHistory from './OrderHistory';
import AddressBook from './AddressBook';
import AccountSettings from './AccountSettings';

export default function AccountContent() {
  const {
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
  } = useAccount();

  if (!isAuthenticated) {
    return <AuthGateway />;
  }

  return (
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6 pb-16 sm:pb-12">
      {/* 1. Member Profile Header Card */}
      <MemberHeader
        customer={customer}
        logout={logout}
        onEditProfile={() => setActiveTab('settings')}
      />

      {/* 2. Navigation Tabs (3 Balanced Pillars: Pesanan, Alamat, Pengaturan) */}
      <div className="grid grid-cols-3 rounded-2xl bg-[#0E1736] p-1.5 border border-white/10 gap-1.5 shadow-lg">
        <button
          type="button"
          onClick={() => setActiveTab('orders')}
          className={`py-2.5 px-2 sm:px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer ${
            activeTab === 'orders'
              ? 'bg-[#CBAC70] text-[#0B132B] shadow-md'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <Package className="w-4 h-4 shrink-0" />
          <span className="truncate">Pesanan ({orders.length})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('addresses')}
          className={`py-2.5 px-2 sm:px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer ${
            activeTab === 'addresses'
              ? 'bg-[#CBAC70] text-[#0B132B] shadow-md'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <MapPin className="w-4 h-4 shrink-0" />
          <span className="truncate">Alamat ({customer?.saved_addresses?.length || 0})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('settings')}
          className={`py-2.5 px-2 sm:px-4 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer ${
            activeTab === 'settings'
              ? 'bg-[#CBAC70] text-[#0B132B] shadow-md'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <Settings className="w-4 h-4 shrink-0" />
          <span className="truncate">Pengaturan</span>
        </button>
      </div>

      {/* Tab Panels */}
      <div className="space-y-4">
        {activeTab === 'orders' && (
          <OrderHistory orders={orders} isLoadingOrders={isLoadingOrders} />
        )}

        {activeTab === 'addresses' && (
          <AddressBook
            savedAddresses={customer?.saved_addresses}
            showAddressModal={showAddressModal}
            setShowAddressModal={setShowAddressModal}
            addrName={addrName}
            setAddrName={setAddrName}
            addrPhone={addrPhone}
            setAddrPhone={setAddrPhone}
            addrStreet={addrStreet}
            setAddrStreet={setAddrStreet}
            addrDistrict={addrDistrict}
            setAddrDistrict={setAddrDistrict}
            addrCity={addrCity}
            setAddrCity={setAddrCity}
            addrProvince={addrProvince}
            setAddrProvince={setAddrProvince}
            addrPostal={addrPostal}
            setAddrPostal={setAddrPostal}
            handleAddAddress={handleAddAddress}
          />
        )}

        {activeTab === 'settings' && (
          <AccountSettings customer={customer} updateProfile={updateProfile} />
        )}
      </div>
    </div>
  );
}
