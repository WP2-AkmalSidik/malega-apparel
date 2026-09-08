'use client';

import { useState, useEffect } from 'react';
import { ShippingOption } from '../../../types';
import { shippingCouriers } from '../../../data/products';
import { fetchShippingRatesFromApi } from '../../../lib/api';
import { CartItem } from '../../../types';

interface UseShippingRatesOptions {
  postalCode: string;
  city: string;
  checkoutItems: CartItem[];
  selectedShipping: ShippingOption;
  setSelectedShipping: (s: ShippingOption) => void;
}

export function useShippingRates({
  postalCode,
  city,
  checkoutItems,
  selectedShipping,
  setSelectedShipping,
}: UseShippingRatesOptions) {
  const [couriersList, setCouriersList] = useState<ShippingOption[]>(shippingCouriers);
  const [isLoadingRates, setIsLoadingRates] = useState<boolean>(false);
  const [shippingSource, setShippingSource] = useState<'biteship' | 'default'>('default');

  useEffect(() => {
    let isMounted = true;
    async function loadShippingRates() {
      if (!postalCode) return;
      setIsLoadingRates(true);

      try {
        const rates = await fetchShippingRatesFromApi({
          destination_postal_code: postalCode,
          destination_city: city,
          items: checkoutItems.map((item) => ({
            weight: 350,
            quantity: item.quantity,
          })),
        });

        if (isMounted && rates && rates.length > 0) {
          setCouriersList(rates);
          setShippingSource('biteship');

          const availableRates = rates.filter((r) => !r.disabled && r.available !== false);
          const currentValid = availableRates.find(
            (r) => r.id === selectedShipping.id || r.courier === selectedShipping.courier
          );
          if (currentValid) {
            setSelectedShipping(currentValid);
          } else if (availableRates.length > 0) {
            setSelectedShipping(availableRates[0]);
          } else {
            setSelectedShipping(rates[0]);
          }
        }
      } catch (err) {
        console.warn('Failed to fetch Biteship live rates:', err);
      } finally {
        if (isMounted) setIsLoadingRates(false);
      }
    }

    loadShippingRates();

    return () => {
      isMounted = false;
    };
  }, [postalCode, city, checkoutItems.length]);

  return { couriersList, isLoadingRates, shippingSource };
}
