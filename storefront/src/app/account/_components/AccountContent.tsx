'use client';

import React from 'react';
import { Package, MapPin, Heart, Settings } from 'lucide-react';
import { useWishlist } from '../../../context/WishlistContext';
import { useAccount } from '../_hooks/useAccount';
import AuthGateway from './AuthGateway';
import MemberHeader from './MemberHeader';
import OrderHistory from './OrderHistory';
import AddressBook from './AddressBook';
import WishlistTab from './WishlistTab';
import AccountSettings from './AccountSettings';

export default function AccountContent() {
  const { wishlistProducts } = useWishlist();
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
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">
      {/* 1. Member Profile Header Card */}
      <MemberHeader customer={customer} logout={logout} />

      {/* 2. Navigation Tabs */}
      <div className="flex rounded-2xl bg-[#0E1736] p-1.5 border border-white/10 gap-1 overflow-x-auto scrollbar-none">
        <button
          type="button"
          onClick={() => setActiveTab('orders')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'orders'
              ? 'bg-[#CBAC70] text-[#0B132B] shadow'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <Package className="w-4 h-4" />
          <span>Riwayat Pesanan ({orders.length})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('addresses')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'addresses'
              ? 'bg-[#CBAC70] text-[#0B132B] shadow'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <MapPin className="w-4 h-4" />
          <span>Buku Alamat ({customer?.saved_addresses?.length || 0})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('wishlist')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'wishlist'
              ? 'bg-[#CBAC70] text-[#0B132B] shadow'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <Heart className="w-4 h-4" />
          <span>Wishlist ({wishlistProducts.length})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('settings')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'settings'
              ? 'bg-[#CBAC70] text-[#0B132B] shadow'
              : 'text-slate-400 hover:text-white'
          }`}
        >
          <Settings className="w-4 h-4" />
          <span>Pengaturan Akun</span>
        </button>
      </div>

      {/* 3. Tab Panels */}
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

        {activeTab === 'wishlist' && <WishlistTab wishlistProducts={wishlistProducts} />}

        {activeTab === 'settings' && (
          <AccountSettings customer={customer} updateProfile={updateProfile} />
        )}
      </div>
    </div>
  );
}
