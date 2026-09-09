'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { CustomerProfile, Address } from '../types';

interface AuthContextType {
  customer: CustomerProfile | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (emailOrPhone: string, password: string) => Promise<{ success: boolean; message: string }>;
  loginWithGoogle: (credential: string) => Promise<{ success: boolean; message: string }>;
  register: (data: { name: string; email: string; phone: string; password: string; marketing_opt_in?: boolean }) => Promise<{ success: boolean; message: string }>;
  logout: () => Promise<void>;
  updateProfile: (data: Partial<CustomerProfile>) => Promise<boolean>;
  addSavedAddress: (address: Address) => Promise<boolean>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

const ensureCsrfCookie = async () => {
  try {
    const backendRoot = API_BASE.replace(/\/api\/v1\/?$/, '');
    await fetch(`${backendRoot}/sanctum/csrf-cookie`, {
      method: 'GET',
      credentials: 'include',
    });
  } catch (e) {
    // Non-blocking fallback
  }
};

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [customer, setCustomer] = useState<CustomerProfile | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  // Initialize from localStorage on mount & verify session
  useEffect(() => {
    try {
      const savedToken = localStorage.getItem('malega_customer_token');
      const savedCustomer = localStorage.getItem('malega_customer_data');

      if (savedToken && savedCustomer) {
        setToken(savedToken);
        setCustomer(JSON.parse(savedCustomer));
        // Verify with backend silently with credentials included
        fetch(`${API_BASE}/customers/me`, {
          headers: {
            Authorization: `Bearer ${savedToken}`,
            Accept: 'application/json',
          },
          credentials: 'include',
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
      await ensureCsrfCookie();

      const res = await fetch(`${API_BASE}/customers/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        credentials: 'include',
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

  const loginWithGoogle = async (credential: string) => {
    try {
      await ensureCsrfCookie();

      const res = await fetch(`${API_BASE}/customers/google`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ credential }),
      });

      const data = await res.json();

      if (data.success && data.data) {
        setToken(data.data.token);
        setCustomer(data.data.customer);
        localStorage.setItem('malega_customer_token', data.data.token);
        localStorage.setItem('malega_customer_data', JSON.stringify(data.data.customer));
        return { success: true, message: data.message || 'Login dengan Google berhasil.' };
      }

      return { success: false, message: data.message || 'Login dengan Google gagal. Silakan coba kembali.' };
    } catch (err) {
      return { success: false, message: 'Gagal terhubung ke server autentikasi Malega. Silakan coba lagi.' };
    }
  };

  const register = async (data: { name: string; email: string; phone: string; password: string; marketing_opt_in?: boolean }) => {
    try {
      await ensureCsrfCookie();

      const res = await fetch(`${API_BASE}/customers/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        credentials: 'include',
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

  const logout = async () => {
    try {
      if (token) {
        await fetch(`${API_BASE}/customers/logout`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
          },
          credentials: 'include',
        });
      }
    } catch (e) {
      // Continue cleanup on client
    } finally {
      setToken(null);
      setCustomer(null);
      localStorage.removeItem('malega_customer_token');
      localStorage.removeItem('malega_customer_data');
    }
  };

  const updateProfile = async (data: Partial<CustomerProfile>) => {
    if (!token || !customer) return false;

    try {
      const res = await fetch(`${API_BASE}/customers/profile`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        },
        credentials: 'include',
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
        loginWithGoogle,
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
