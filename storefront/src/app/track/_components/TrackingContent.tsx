'use client';

import React from 'react';
import { useSearchParams } from 'next/navigation';
import { useTracking } from '../_hooks/useTracking';
import TrackSearchBar from './TrackSearchBar';
import EmptyState from './EmptyState';
import OrderHeroCard from './OrderHeroCard';
import TabNavigation from './TabNavigation';
import TimelineTab from './TimelineTab';
import PackageTab from './PackageTab';
import InvoiceTab from './InvoiceTab';
import MobileActionBar from './MobileActionBar';
import LuxuryToast from './LuxuryToast';

export default function TrackingContent() {
  const searchParams = useSearchParams();
  const initialQuery =
    searchParams.get('q') ||
    searchParams.get('order') ||
    searchParams.get('order_number') ||
    searchParams.get('merchantOrderId') ||
    searchParams.get('tracking_number') ||
    '';

  const {
    searchQuery,
    setSearchQuery,
    activeTab,
    setActiveTab,
    isLoading,
    order,
    error,
    copiedKey,
    isGeneratingInvoice,
    toast,
    progressStep,
    milestones,
    courierCompany,
    waText,
    copyToClipboard,
    handleCreatePaymentInvoice,
    fetchTracking,
    handleSearch,
  } = useTracking({ initialQuery });

  return (
    <div className="min-h-screen bg-[#060913] text-[#FDFCFF] pb-24 sm:pb-16">
      {/* Top Ambient Glow Accent */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-48 bg-gradient-to-b from-[#CBAC70]/10 via-transparent to-transparent blur-3xl pointer-events-none" />

      <div className="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-10 space-y-6 sm:space-y-8 relative z-10">
        {/* Search & Header Section */}
        <TrackSearchBar
          searchQuery={searchQuery}
          setSearchQuery={setSearchQuery}
          isLoading={isLoading}
          hasOrder={!!order}
          onSearch={handleSearch}
          onRefresh={() => order && fetchTracking(order.orderNumber)}
        />

        {/* Error Banner */}
        {error && (
          <div className="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs text-center space-y-1 animate-fade-in">
            <p className="font-semibold">{error}</p>
          </div>
        )}

        {/* Empty State */}
        {!order && !isLoading && !error && <EmptyState />}

        {/* Active Order Card */}
        {order && (
          <div className="space-y-6 animate-fade-in">
            <OrderHeroCard
              order={order}
              progressStep={progressStep}
              courierCompany={courierCompany}
              copiedKey={copiedKey}
              isGeneratingInvoice={isGeneratingInvoice}
              onCopy={copyToClipboard}
              onPayNow={handleCreatePaymentInvoice}
            />

            <TabNavigation activeTab={activeTab} setActiveTab={setActiveTab} />

            {activeTab === 'package' && <PackageTab order={order} />}
            {activeTab === 'timeline' && (
              <TimelineTab
                order={order}
                courierCompany={courierCompany}
                milestones={milestones}
              />
            )}
            {activeTab === 'invoice' && (
              <InvoiceTab order={order} courierCompany={courierCompany} />
            )}
          </div>
        )}
      </div>

      {/* Floating Sticky Mobile Quick Action Bar */}
      {order && (
        <MobileActionBar
          order={order}
          waText={waText}
          isGeneratingInvoice={isGeneratingInvoice}
          onPayNow={handleCreatePaymentInvoice}
        />
      )}

      {/* Toast Notification */}
      <LuxuryToast toast={toast} />
    </div>
  );
}
