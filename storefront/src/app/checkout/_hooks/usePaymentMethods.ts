'use client';

import { useState, useEffect } from 'react';
import { PaymentMethod } from '../../../types';
import { paymentGateways } from '../../../data/products';
import { fetchPaymentMethodsFromApi } from '../../../lib/api';

interface UsePaymentMethodsOptions {
  subtotal: number;
  selectedPayment: PaymentMethod;
  setSelectedPayment: (p: PaymentMethod) => void;
}

export function usePaymentMethods({
  subtotal,
  selectedPayment,
  setSelectedPayment,
}: UsePaymentMethodsOptions) {
  const [paymentsList, setPaymentsList] = useState<PaymentMethod[]>(paymentGateways);
  const [isLoadingPayments, setIsLoadingPayments] = useState<boolean>(false);

  useEffect(() => {
    let isMounted = true;
    async function loadPaymentMethods() {
      setIsLoadingPayments(true);
      try {
        const methods = await fetchPaymentMethodsFromApi(Math.max(10000, subtotal || 100000));
        if (isMounted && methods && methods.length > 0) {
          setPaymentsList(methods);

          const exists = methods.find(
            (m) => m.id === selectedPayment.id || m.duitkuCode === selectedPayment.duitkuCode
          );
          if (exists) {
            setSelectedPayment(exists);
          } else {
            setSelectedPayment(methods[0]);
          }
        }
      } catch (err) {
        console.warn('Failed to fetch Duitku payment methods:', err);
      } finally {
        if (isMounted) setIsLoadingPayments(false);
      }
    }

    loadPaymentMethods();

    return () => {
      isMounted = false;
    };
  }, [subtotal]);

  return { paymentsList, isLoadingPayments };
}
