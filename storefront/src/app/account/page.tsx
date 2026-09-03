'use client';

import React, { useState, useEffect, Suspense } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { 
  User, 
  Package, 
  Heart, 
  MapPin, 
  Settings, 
  LogOut, 
  ShieldCheck, 
  Truck, 
  Clock, 
  Sparkles, 
  CheckCircle2, 
  ArrowRight,
  Plus,
  Mail,
  Phone,
  Edit3,
  UserPlus
} from 'lucide-react';
import { useAuth } from '../../context/AuthContext';
import { useWishlist } from '../../context/WishlistContext';
import { CustomerPastOrder } from '../../types';

function CustomerAccountContent() {
  const searchParams = useSearchParams();
  const { customer, isAuthenticated, token, logout, updateProfile, login, register } = useAuth();
  const { wishlistProducts } = useWishlist();

  const [activeTab, setActiveTab] = useState<'orders' | 'addresses' | 'wishlist' | 'settings'>('orders');
  const [orders, setOrders] = useState<CustomerPastOrder[]>([]);
  const [isLoadingOrders, setIsLoadingOrders] = useState<boolean>(false);

  // Auth Form State (if not logged in)
  const [isLoginMode, setIsLoginMode] = useState<boolean>(searchParams?.get('tab') !== 'register');
  const [loginEmailOrPhone, setLoginEmailOrPhone] = useState('');
  const [loginPassword, setLoginPassword] = useState('');
  const [regName, setRegName] = useState('');
  const [regEmail, setRegEmail] = useState('');
  const [regPhone, setRegPhone] = useState('');
  const [regPassword, setRegPassword] = useState('');
  const [regMarketing, setRegMarketing] = useState(true);
  const [authError, setAuthError] = useState('');
  const [authSuccess, setAuthSuccess] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Sync tab with URL search params if changed
  useEffect(() => {
    if (searchParams?.get('tab') === 'register') {
      setIsLoginMode(false);
    } else if (searchParams?.get('tab') === 'login') {
      setIsLoginMode(true);
    }
  }, [searchParams]);

  // New Address State
  const [showAddressModal, setShowAddressModal] = useState(false);
  const [addrName, setAddrName] = useState('');
  const [addrPhone, setAddrPhone] = useState('');
  const [addrStreet, setAddrStreet] = useState('');
  const [addrDistrict, setAddrDistrict] = useState('');
  const [addrCity, setAddrCity] = useState('');
  const [addrProvince, setAddrProvince] = useState('');
  const [addrPostal, setAddrPostal] = useState('');

  const API_BASE = process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

  // Fetch orders when logged in
  useEffect(() => {
    if (isAuthenticated && token) {
      setIsLoadingOrders(true);
      fetch(`${API_BASE}/customers/orders`, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then(res => res.json())
        .then(data => {
          if (data.success && Array.isArray(data.data)) {
            setOrders(data.data);
          }
        })
        .catch(err => console.error('Error fetching customer orders:', err))
        .finally(() => setIsLoadingOrders(false));
    }
  }, [isAuthenticated, token]);

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setAuthError('');
    setIsSubmitting(true);

    const res = await login(loginEmailOrPhone, loginPassword);
    setIsSubmitting(false);

    if (!res.success) {
      setAuthError(res.message);
    }
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setAuthError('');
    setIsSubmitting(true);

    const res = await register({
      name: regName,
      email: regEmail,
      phone: regPhone,
      password: regPassword,
      marketing_opt_in: regMarketing
    });
    setIsSubmitting(false);

    if (!res.success) {
      setAuthError(res.message);
    }
  };

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
      isDefault: (customer.saved_addresses?.length || 0) === 0
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

  // If Guest (Not Authenticated), Show Luxury Auth Gateway
  if (!isAuthenticated) {
    return (
      <div className="max-w-md mx-auto px-4 py-10 sm:py-16">
        <div className="rounded-3xl bg-[#0E1736] border border-[#CBAC70]/30 shadow-2xl p-6 sm:p-8 space-y-6">
          
          {/* Header */}
          <div className="text-center space-y-2">
            <div className="w-12 h-12 rounded-2xl bg-[#CBAC70]/15 border border-[#CBAC70]/40 flex items-center justify-center text-[#CBAC70] mx-auto shadow-md">
              {isLoginMode ? <User className="w-6 h-6" /> : <UserPlus className="w-6 h-6" />}
            </div>
            <h1 className="text-2xl font-black text-white uppercase tracking-wide">
              {isLoginMode ? 'Masuk ke Akun Malega' : 'Daftar Anggota Eksklusif'}
            </h1>
            <p className="text-xs text-slate-400 leading-relaxed">
              {isLoginMode 
                ? 'Akses riwayat pesanan, status pengiriman langsung, dan status loyalty member Anda.' 
                : 'Buat akun dalam 30 detik untuk menikmati voucher diskon member dan update rilisan limited SS26.'}
            </p>
          </div>

          {/* Explicitly Interactive Toggle Tabs */}
          <div className="flex rounded-2xl bg-[#070D1F] p-1.5 border border-white/10 gap-1 relative z-20">
            <button
              type="button"
              onClick={(e) => {
                e.preventDefault();
                setIsLoginMode(true);
                setAuthError('');
              }}
              className={`flex-1 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
                isLoginMode
                  ? 'bg-gradient-to-r from-[#CBAC70] to-[#A58645] text-[#0B132B] shadow-md font-black'
                  : 'text-slate-400 hover:text-white hover:bg-white/5'
              }`}
            >
              <User className="w-3.5 h-3.5" />
              <span>Masuk (Login)</span>
            </button>
            <button
              type="button"
              onClick={(e) => {
                e.preventDefault();
                setIsLoginMode(false);
                setAuthError('');
              }}
              className={`flex-1 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
                !isLoginMode
                  ? 'bg-gradient-to-r from-[#CBAC70] to-[#A58645] text-[#0B132B] shadow-md font-black'
                  : 'text-slate-400 hover:text-white hover:bg-white/5'
              }`}
            >
              <UserPlus className="w-3.5 h-3.5" />
              <span>Daftar Baru</span>
            </button>
          </div>

          {authError && (
            <div className="p-3.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-rose-400 animate-ping shrink-0" />
              <span>{authError}</span>
            </div>
          )}

          {/* 1. Login Form */}
          {isLoginMode && (
            <form onSubmit={handleLogin} className="space-y-4">
              <div>
                <label className="block text-xs font-medium text-slate-300 mb-1">Email atau No. WhatsApp</label>
                <input
                  type="text"
                  required
                  value={loginEmailOrPhone}
                  onChange={(e) => setLoginEmailOrPhone(e.target.value)}
                  placeholder="contoh@email.com atau 08123456789"
                  className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70] transition"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-slate-300 mb-1">Kata Sandi</label>
                <input
                  type="password"
                  required
                  value={loginPassword}
                  onChange={(e) => setLoginPassword(e.target.value)}
                  placeholder="••••••••"
                  className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70] transition"
                />
              </div>

              <button
                type="submit"
                disabled={isSubmitting}
                className="w-full py-3 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#A58645] hover:from-[#E3CD99] hover:to-[#CBAC70] text-[#0B132B] font-black text-xs shadow-lg transition active:scale-95 disabled:opacity-50 cursor-pointer"
              >
                {isSubmitting ? 'Memverifikasi...' : 'Masuk Sekarang'}
              </button>
            </form>
          )}

          {/* 2. Register Form */}
          {!isLoginMode && (
            <form onSubmit={handleRegister} className="space-y-3.5">
              <div>
                <label className="block text-xs font-medium text-slate-300 mb-1">Nama Lengkap</label>
                <input
                  type="text"
                  required
                  value={regName}
                  onChange={(e) => setRegName(e.target.value)}
                  placeholder="Nama Lengkap Anda"
                  className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70] transition"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-slate-300 mb-1">Email Aktif</label>
                <input
                  type="email"
                  required
                  value={regEmail}
                  onChange={(e) => setRegEmail(e.target.value)}
                  placeholder="contoh@email.com"
                  className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70] transition"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-slate-300 mb-1">No. WhatsApp</label>
                <input
                  type="text"
                  required
                  value={regPhone}
                  onChange={(e) => setRegPhone(e.target.value)}
                  placeholder="081234567890"
                  className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70] transition"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-slate-300 mb-1">Kata Sandi</label>
                <input
                  type="password"
                  required
                  value={regPassword}
                  onChange={(e) => setRegPassword(e.target.value)}
                  placeholder="Minimal 6 karakter"
                  className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70] transition"
                />
              </div>

              <div className="pt-1">
                <label className="flex items-start gap-2 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={regMarketing}
                    onChange={(e) => setRegMarketing(e.target.checked)}
                    className="rounded border-slate-700 bg-[#070D1F] text-[#CBAC70] focus:ring-[#CBAC70] mt-0.5 cursor-pointer"
                  />
                  <span className="text-[11px] text-slate-400 leading-tight">
                    Saya ingin menerima penawaran rilis limited drops, diskon eksklusif, dan promo via WhatsApp & Email.
                  </span>
                </label>
              </div>

              <button
                type="submit"
                disabled={isSubmitting}
                className="w-full py-3 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#A58645] hover:from-[#E3CD99] hover:to-[#CBAC70] text-[#0B132B] font-black text-xs shadow-lg transition active:scale-95 disabled:opacity-50 cursor-pointer"
              >
                {isSubmitting ? 'Mendaftarkan Akun...' : 'Buat Akun Member Sekarang'}
              </button>
            </form>
          )}

          {/* Direct Track as Guest Option */}
          <div className="pt-4 border-t border-white/5 text-center">
            <Link
              href="/track"
              className="text-xs text-slate-400 hover:text-[#CBAC70] transition inline-flex items-center gap-1 font-mono"
            >
              <span>Lacak Status Pesanan Tanpa Login (Guest Tracking)</span>
              <ArrowRight className="w-3 h-3" />
            </Link>
          </div>

        </div>
      </div>
    );
  }

  // Authenticated Member Dashboard
  return (
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">
      
      {/* 1. Member Profile Header Card */}
      <div className="rounded-3xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-6 border border-[#CBAC70]/30 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div className="flex items-center gap-4">
          <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-black text-xl shadow-lg shrink-0">
            {customer?.name.substring(0, 2).toUpperCase()}
          </div>
          <div className="space-y-1">
            <div className="flex items-center gap-2 flex-wrap">
              <h1 className="text-xl sm:text-2xl font-black text-white">{customer?.name}</h1>
              <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold ${
                customer?.membership_tier === 'VIP Platinum'
                  ? 'bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/50'
                  : customer?.membership_tier === 'Gold'
                  ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40'
                  : 'bg-slate-800 text-slate-300 border border-slate-700'
              }`}>
                ★ {customer?.membership_tier} Member
              </span>
            </div>
            <p className="text-xs text-slate-400 font-mono flex items-center gap-3">
              <span>✉ {customer?.email}</span>
              <span>•</span>
              <span>📱 {customer?.phone}</span>
            </p>
          </div>
        </div>

        {/* Member Stats & Logout */}
        <div className="flex items-center gap-4 self-end md:self-auto">
          <div className="text-right px-4 py-2 rounded-2xl bg-black/40 border border-white/5">
            <p className="text-[10px] font-mono uppercase text-[#CBAC70]">Akumulasi Belanja</p>
            <p className="text-sm sm:text-base font-black font-mono text-white">
              {customer?.formatted_spend || `Rp ${(customer?.total_spend || 0).toLocaleString('id-ID')}`}
            </p>
          </div>

          <button
            type="button"
            onClick={logout}
            className="p-3 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition cursor-pointer"
            title="Keluar (Logout)"
          >
            <LogOut className="w-5 h-5" />
          </button>
        </div>
      </div>

      {/* 2. Navigation Tabs */}
      <div className="flex rounded-2xl bg-[#0E1736] p-1.5 border border-white/10 gap-1 overflow-x-auto scrollbar-none">
        <button
          type="button"
          onClick={() => setActiveTab('orders')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'orders' ? 'bg-[#CBAC70] text-[#0B132B] shadow' : 'text-slate-400 hover:text-white'
          }`}
        >
          <Package className="w-4 h-4" />
          <span>Riwayat Pesanan ({orders.length})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('addresses')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'addresses' ? 'bg-[#CBAC70] text-[#0B132B] shadow' : 'text-slate-400 hover:text-white'
          }`}
        >
          <MapPin className="w-4 h-4" />
          <span>Buku Alamat ({customer?.saved_addresses?.length || 0})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('wishlist')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'wishlist' ? 'bg-[#CBAC70] text-[#0B132B] shadow' : 'text-slate-400 hover:text-white'
          }`}
        >
          <Heart className="w-4 h-4" />
          <span>Wishlist ({wishlistProducts.length})</span>
        </button>

        <button
          type="button"
          onClick={() => setActiveTab('settings')}
          className={`px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 cursor-pointer ${
            activeTab === 'settings' ? 'bg-[#CBAC70] text-[#0B132B] shadow' : 'text-slate-400 hover:text-white'
          }`}
        >
          <Settings className="w-4 h-4" />
          <span>Pengaturan Akun</span>
        </button>
      </div>

      {/* 3. Tab Content */}
      <div className="space-y-4">
        {/* TAB 1: ORDERS */}
        {activeTab === 'orders' && (
          <div className="space-y-4">
            {isLoadingOrders ? (
              <div className="py-12 text-center text-slate-400 font-mono text-xs">
                Memuat riwayat pesanan...
              </div>
            ) : orders.length > 0 ? (
              orders.map(order => (
                <div
                  key={order.id}
                  className="rounded-3xl bg-[#0E1736] border border-white/10 p-5 sm:p-6 shadow-xl space-y-4"
                >
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-white/5 gap-2">
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-mono font-bold text-sm text-slate-100">{order.order_number}</span>
                        <span className="text-xs text-slate-400 font-mono">• {order.created_at}</span>
                      </div>
                    </div>

                    <div className="flex items-center gap-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-bold font-mono ${
                        order.status === 'paid' || order.status === 'delivered'
                          ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'
                          : order.status === 'shipped'
                          ? 'bg-blue-500/15 text-blue-400 border border-blue-500/30'
                          : 'bg-amber-500/15 text-amber-400 border border-amber-500/30'
                      }`}>
                        {order.status_label || order.status.toUpperCase()}
                      </span>

                      <Link
                        href={`/track?order=${order.order_number}`}
                        className="px-3 py-1 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition flex items-center gap-1"
                      >
                        <Truck className="w-3.5 h-3.5 text-[#CBAC70]" />
                        <span>Lacak Pengiriman</span>
                      </Link>
                    </div>
                  </div>

                  {/* Item List */}
                  <div className="space-y-2">
                    {order.items.map((item, idx) => (
                      <div key={idx} className="flex items-center justify-between text-xs py-1">
                        <div>
                          <p className="font-semibold text-slate-200">{item.title}</p>
                          <p className="text-[10px] text-slate-400 font-mono">SKU: {item.sku} • {item.quantity} pcs</p>
                        </div>
                        <p className="font-mono font-bold text-slate-300">
                          Rp {item.subtotal.toLocaleString('id-ID')}
                        </p>
                      </div>
                    ))}
                  </div>

                  {/* Total & Courier */}
                  <div className="pt-3 border-t border-white/5 flex items-center justify-between">
                    <div>
                      {order.shipping?.courier && (
                        <p className="text-xs text-slate-400 font-mono">
                          Kurir: <span className="text-slate-200">{order.shipping.courier}</span> ({order.shipping.waybill || 'Sedang diproses'})
                        </p>
                      )}
                    </div>
                    <div className="text-right">
                      <span className="text-[10px] text-slate-400 uppercase font-mono mr-2">Total Belanja:</span>
                      <span className="text-base font-bold font-mono text-[#CBAC70]">{order.formatted_total}</span>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className="py-16 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 space-y-3">
                <Package className="w-12 h-12 text-slate-500 mx-auto" />
                <p className="text-sm font-bold text-slate-300">Belum Ada Riwayat Pesanan</p>
                <p className="text-xs text-slate-500">Mulai belanja artikel streetwear favorit Anda sekarang.</p>
                <Link
                  href="/products"
                  className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs shadow hover:bg-[#E3CD99] transition"
                >
                  <span>Mulai Belanja</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>
              </div>
            )}
          </div>
        )}

        {/* TAB 2: ADDRESSES */}
        {activeTab === 'addresses' && (
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <h3 className="text-sm font-bold text-slate-200">Alamat Pengiriman Tersimpan</h3>
              <button
                type="button"
                onClick={() => setShowAddressModal(true)}
                className="px-3.5 py-1.5 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs shadow flex items-center gap-1.5 transition hover:bg-[#E3CD99] cursor-pointer"
              >
                <Plus className="w-4 h-4" />
                <span>Tambah Alamat Baru</span>
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {customer?.saved_addresses && customer.saved_addresses.length > 0 ? (
                customer.saved_addresses.map((addr, idx) => (
                  <div
                    key={idx}
                    className="p-5 rounded-3xl bg-[#0E1736] border border-white/10 space-y-2 relative"
                  >
                    {addr.isDefault && (
                      <span className="absolute top-4 right-4 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                        Utama
                      </span>
                    )}
                    <p className="font-bold text-sm text-slate-100">{addr.name}</p>
                    <p className="text-xs text-slate-400 font-mono">{addr.phone}</p>
                    <p className="text-xs text-slate-300">{addr.street}</p>
                    <p className="text-xs text-slate-400">{addr.district}, {addr.city}, {addr.province} {addr.postalCode}</p>
                  </div>
                ))
              ) : (
                <div className="col-span-2 py-12 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 text-slate-400 text-xs">
                  Belum ada alamat tersimpan. Tambahkan alamat untuk checkout lebih cepat.
                </div>
              )}
            </div>
          </div>
        )}

        {/* TAB 3: WISHLIST */}
        {activeTab === 'wishlist' && (
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <h3 className="text-sm font-bold text-slate-200">Produk yang Disimpan ({wishlistProducts.length})</h3>
              <Link
                href="/favorites"
                className="text-xs text-[#CBAC70] hover:underline font-semibold"
              >
                Buka Halaman Wishlist Penuh →
              </Link>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
              {wishlistProducts.map(p => (
                <Link
                  key={p.id}
                  href={`/products/${p.slug}`}
                  className="rounded-2xl bg-[#0E1736] border border-white/10 hover:border-[#CBAC70]/40 p-3 space-y-2 group transition"
                >
                  <img
                    src={p.colors[0]?.image || p.gallery[0]}
                    alt={p.title}
                    className="w-full aspect-square rounded-xl object-cover"
                  />
                  <p className="font-bold text-xs text-slate-200 group-hover:text-[#CBAC70] transition line-clamp-1">{p.title}</p>
                  <p className="font-mono font-bold text-xs text-[#CBAC70]">Rp {p.price.toLocaleString('id-ID')}</p>
                </Link>
              ))}
            </div>
          </div>
        )}

        {/* TAB 4: SETTINGS & MARKETING PREFERENCES */}
        {activeTab === 'settings' && (
          <div className="rounded-3xl bg-[#0E1736] border border-white/10 p-6 space-y-6">
            <h3 className="text-base font-bold text-slate-100">Preferensi Akun & Pemasaran</h3>

            <div className="space-y-4">
              <div className="flex items-center justify-between p-4 rounded-2xl bg-[#070D1F] border border-white/5">
                <div className="space-y-1">
                  <p className="text-xs font-bold text-slate-200">Langganan Notifikasi Rilis Drop & Voucher Promo</p>
                  <p className="text-[11px] text-slate-400">
                    Dapatkan kabar rilis artikel limited edition dan voucher potongan harga langsung via WhatsApp & Email.
                  </p>
                </div>
                <input
                  type="checkbox"
                  checked={customer?.marketing_opt_in ?? true}
                  onChange={async (e) => {
                    await updateProfile({ marketing_opt_in: e.target.checked });
                  }}
                  className="w-5 h-5 rounded border-slate-700 bg-[#0E1736] text-[#CBAC70] focus:ring-[#CBAC70] cursor-pointer"
                />
              </div>
            </div>
          </div>
        )}
      </div>

    </div>
  );
}

export default function CustomerAccountPage() {
  return (
    <Suspense fallback={<div className="py-20 text-center text-xs font-mono text-slate-400">Memuat portal akun...</div>}>
      <CustomerAccountContent />
    </Suspense>
  );
}
