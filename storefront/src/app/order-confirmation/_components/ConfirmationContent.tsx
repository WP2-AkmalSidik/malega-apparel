'use client';

import React from 'react';
import { useOrderConfirmation } from '../_hooks/useOrderConfirmation';
import ConfirmationHero from './ConfirmationHero';
import OrderTimeline from './OrderTimeline';
import OrderedItems from './OrderedItems';
import PaymentSummary from './PaymentSummary';
import LuxuryToast from '../../../components/LuxuryToast';

export default function ConfirmationContent() {
  const {
    order,
    toast,
    copiedInvoice,
    copiedResi,
    copyText,
    waText,
  } = useOrderConfirmation();

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
      <ConfirmationHero
        order={order}
        copiedInvoice={copiedInvoice}
        copiedResi={copiedResi}
        copyText={copyText}
      />

      <OrderTimeline order={order} />

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <OrderedItems order={order} />
        <PaymentSummary order={order} waText={waText} />
      </div>

      <LuxuryToast toast={toast} />
    </div>
  );
}
