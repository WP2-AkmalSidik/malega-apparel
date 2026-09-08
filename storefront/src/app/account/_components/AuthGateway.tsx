'use client';

import React from 'react';
import Link from 'next/link';
import { User, UserPlus, ArrowRight } from 'lucide-react';
import { useAuthForm } from '../_hooks/useAuthForm';

export default function AuthGateway() {
  const {
    isLoginMode,
    setIsLoginMode,
    loginEmailOrPhone,
    setLoginEmailOrPhone,
    loginPassword,
    setLoginPassword,
    regName,
    setRegName,
    regEmail,
    setRegEmail,
    regPhone,
    setRegPhone,
    regPassword,
    setRegPassword,
    regMarketing,
    setRegMarketing,
    authError,
    setAuthError,
    isSubmitting,
    handleLogin,
    handleRegister,
  } = useAuthForm();

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
              <label className="block text-xs font-medium text-slate-300 mb-1">
                Email atau No. WhatsApp
              </label>
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
                  Saya ingin menerima penawaran rilis limited drops, diskon eksklusif, dan promo via
                  WhatsApp & Email.
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
