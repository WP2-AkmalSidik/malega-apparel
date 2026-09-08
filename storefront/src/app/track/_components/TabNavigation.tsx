import React from 'react';
import { Truck, Package, Receipt } from 'lucide-react';

interface TabNavigationProps {
  activeTab: 'timeline' | 'package' | 'invoice';
  setActiveTab: (tab: 'timeline' | 'package' | 'invoice') => void;
}

export default function TabNavigation({ activeTab, setActiveTab }: TabNavigationProps) {
  const tabs = [
    { id: 'timeline' as const, label: 'Status', icon: Truck },
    { id: 'package' as const, label: 'Busana', icon: Package },
    { id: 'invoice' as const, label: 'Faktur', icon: Receipt },
  ];

  return (
    <div className="p-1 sm:p-1.5 rounded-2xl bg-[#0B132B]/90 backdrop-blur-md border border-white/10 grid grid-cols-3 gap-1 text-xs shadow-lg">
      {tabs.map((tab) => {
        const Icon = tab.icon;
        return (
          <button
            key={tab.id}
            type="button"
            onClick={() => setActiveTab(tab.id)}
            className={`py-2 px-1 sm:py-2.5 sm:px-3 rounded-xl font-semibold transition-all flex items-center justify-center gap-1.5 ${
              activeTab === tab.id
                ? 'bg-[#CBAC70] text-[#060913] font-bold shadow-[0_2px_10px_rgba(203,172,112,0.35)]'
                : 'text-slate-400 hover:text-white'
            }`}
          >
            <Icon className="w-3.5 h-3.5 shrink-0" />
            <span className="tracking-wide">{tab.label}</span>
          </button>
        );
      })}
    </div>
  );
}
