'use client';

import React, { Suspense } from 'react';
import AccountContent from './_components/AccountContent';

export default function CustomerAccountPage() {
  return (
    <Suspense
      fallback={
        <div className="py-20 text-center text-xs font-mono text-slate-400">
          Memuat portal akun...
        </div>
      }
    >
      <AccountContent />
    </Suspense>
  );
}
