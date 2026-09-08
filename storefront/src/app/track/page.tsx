'use client';

import React, { Suspense } from 'react';
import TrackingContent from './_components/TrackingContent';

export default function StorefrontTrackingPage() {
  return (
    <Suspense
      fallback={
        <div className="min-h-[50vh] flex items-center justify-center bg-[#060913]">
          <div className="w-8 h-8 rounded-full border-2 border-[#CBAC70] border-t-transparent animate-spin" />
        </div>
      }
    >
      <TrackingContent />
    </Suspense>
  );
}
