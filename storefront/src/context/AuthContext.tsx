'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { CustomerProfile, Address } from '../types';

interface AuthContextType {
  customer: CustomerProfile | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (emailOrPhone: string, password: string) => Promise<{ success: boolean; message: string }>;
  register: (data: { name: string; email: string; phone: string; password: string; marketing_opt_in?: boolean }) => Promise<{ success: boolean; message: string }>;
  logout: () => void;
  updateProfile: (data: Partial<CustomerProfile>) => Promise<boolean>;
  addSavedAddress: (address: Address) => Promise<boolean>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [customer, setCustomer] = useState<CustomerProfile | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  // Initialize from localStorage on mount
  useEffect(() => {
    try {
      const savedToken = localStorage.getItem('malega_customer_token');
      const savedCustomer = localStorage.getItem('malega_customer_data');

      if (savedToken && savedCustomer) {
        setToken(savedToken);
        setCustomer(JSON.parse(savedCustomer));
        // Verify with backend silently
        fetch(`${API_BASE}/customers/me`, {
          headers: { Authorization: `Bearer ${savedToken}` }
        })
          .then(res => res.json())
          .then(resData => {
            if (resData.success && resData.data) {
              setCustomer(resData.data);
              localStorage.setItem('malega_customer_data', JSON.stringify(resData.data));
            }
          })
          .catch(() => {});
      }
    } catch (e) {
      console.error('Error loading auth from localStorage:', e);
    } finally {
      setIsLoading(false);
    }
  }, []);

  const login = async (emailOrPhone: string, password: string) => {
    try {
      const res = await fetch(`${API_BASE}/customers/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email_or_phone: emailOrPhone, password })
      });

      const data = await res.json();

      if (data.success && data.data) {
        setToken(data.data.token);
        setCustomer(data.data.customer);
        localStorage.setItem('malega_customer_token', data.data.token);
        localStorage.setItem('malega_customer_data', JSON.stringify(data.data.customer));
        return { success: true, message: data.message || 'Login berhasil.' };
      }

      return { success: false, message: data.message || 'Login gagal.' };
    } catch (err: any) {
      return { success: false, message: 'Gagal terhubung ke server. Silakan coba lagi.' };
    }
  };

  const register = async (data: { name: string; email: string; phone: string; password: string; marketing_opt_in?: boolean }) => {
    try {
      const res = await fetch(`${API_BASE}/customers/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const resData = await res.json();

      if (resData.success && resData.data) {
        setToken(resData.data.token);
        setCustomer(resData.data.customer);
        localStorage.setItem('malega_customer_token', resData.data.token);
        localStorage.setItem('malega_customer_data', JSON.stringify(resData.data.customer));
        return { success: true, message: resData.message || 'Pendaftaran berhasil.' };
      }

      return { success: false, message: resData.message || 'Pendaftaran gagal.' };
    } catch (err) {
      return { success: false, message: 'Gagal terhubung ke server.' };
    }
  };

  const logout = () => {
    setToken(null);
    setCustomer(null);
    localStorage.removeItem('malega_customer_token');
    localStorage.removeItem('malega_customer_data');
  };

  const updateProfile = async (data: Partial<CustomerProfile>) => {
    if (!token || !customer) return false;

    try {
      const res = await fetch(`${API_BASE}/customers/profile`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify(data)
      });

      const resData = await res.json();
      if (resData.success) {
        const updated = { ...customer, ...data };
        setCustomer(updated as CustomerProfile);
        localStorage.setItem('malega_customer_data', JSON.stringify(updated));
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  };

  const addSavedAddress = async (newAddress: Address) => {
    if (!customer) return false;
    const currentAddresses = customer.saved_addresses || [];
    const updatedAddresses = [...currentAddresses, newAddress];
    return await updateProfile({ saved_addresses: updatedAddresses });
  };

  return (
    <AuthContext.Provider
      value={{
        customer,
        token,
        isAuthenticated: !!customer,
        isLoading,
        login,
        register,
        logout,
        updateProfile,
        addSavedAddress
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
