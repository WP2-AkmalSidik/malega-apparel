'use client';

import { useState, useEffect } from 'react';
import { useSearchParams } from 'next/navigation';
import { useAuth } from '../../../context/AuthContext';

export function useAuthForm() {
  const searchParams = useSearchParams();
  const { login, register, loginWithGoogle } = useAuth();

  const [isLoginMode, setIsLoginMode] = useState<boolean>(
    searchParams?.get('tab') !== 'register'
  );
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
      marketing_opt_in: regMarketing,
    });
    setIsSubmitting(false);

    if (!res.success) {
      setAuthError(res.message);
    }
  };

  const handleGoogleSuccess = async (credentialResponse: any) => {
    if (!credentialResponse?.credential) {
      setAuthError('Kredensial autentikasi Google tidak diterima. Silakan coba kembali.');
      return;
    }

    setAuthError('');
    setIsSubmitting(true);

    const res = await loginWithGoogle(credentialResponse.credential);
    setIsSubmitting(false);

    if (!res.success) {
      setAuthError(res.message);
    }
  };

  const handleGoogleError = () => {
    setAuthError('Autentikasi dengan Google dibatalkan atau tidak berhasil. Silakan coba kembali.');
  };

  return {
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
    authSuccess,
    setAuthSuccess,
    isSubmitting,
    handleLogin,
    handleRegister,
    handleGoogleSuccess,
    handleGoogleError,
  };
}
