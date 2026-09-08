'use client';

import React, { Suspense } from 'react';
import { Loader2 } from 'lucide-react';
import ConfirmationContent from './_components/ConfirmationContent';

export default function OrderConfirmationPage() {
  return (
    <Suspense
      fallback={
        <div className="max-w-4xl mx-auto px-4 py-20 text-center space-y-3">
          <Loader2 className="w-8 h-8 animate-spin text-[#CBAC70] mx-auto" />
          <p className="text-xs text-[#94A3B8]">Memuat konfirmasi pesanan...</p>
        </div>
      }
    >
      <ConfirmationContent />
    </Suspense>
  );
}
