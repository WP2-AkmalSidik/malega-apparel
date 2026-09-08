import React from 'react';
import { Search, RefreshCw } from 'lucide-react';

interface TrackSearchBarProps {
  searchQuery: string;
  setSearchQuery: (q: string) => void;
  isLoading: boolean;
  hasOrder: boolean;
  onSearch: (e: React.FormEvent) => void;
  onRefresh: () => void;
}

export default function TrackSearchBar({
  searchQuery,
  setSearchQuery,
  isLoading,
  hasOrder,
  onSearch,
  onRefresh,
}: TrackSearchBarProps) {
  return (
    <div className="space-y-3 text-center sm:text-left">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#CBAC70]/10 border border-[#CBAC70]/30 text-[#CBAC70] text-[11px] font-mono font-semibold tracking-wide">
            <span className="w-2 h-2 rounded-full bg-[#CBAC70] animate-ping" />
            <span>Malega Live Tracking &bull; Atelier Portal</span>
          </div>
          <h1 className="font-display text-2xl sm:text-3xl font-bold tracking-tight text-white mt-1.5">
            Lacak Status Pesanan
          </h1>
        </div>

        {hasOrder && (
          <button
            type="button"
            onClick={onRefresh}
            disabled={isLoading}
            className="self-center sm:self-auto px-3.5 py-1.5 rounded-xl border border-white/10 bg-[#0B132B] hover:bg-[#14204A] text-slate-300 text-xs font-medium transition-all flex items-center gap-2 active:scale-95 disabled:opacity-50"
          >
            <RefreshCw className={`w-3.5 h-3.5 text-sky-400 ${isLoading ? 'animate-spin' : ''}`} />
            <span>Perbarui Data</span>
          </button>
        )}
      </div>

      {/* Minimalist Search Bar */}
      <form onSubmit={onSearch} className="relative max-w-2xl">
        <div className="relative flex items-center rounded-2xl bg-[#0B132B]/90 backdrop-blur-xl border border-white/10 focus-within:border-[#CBAC70] focus-within:ring-2 focus-within:ring-[#CBAC70]/20 shadow-xl transition-all">
          <div className="pl-3.5 text-slate-400">
            <Search className="w-4 h-4" />
          </div>
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Ketik Nomor Pesanan (contoh: MLG-2026...)"
            className="w-full bg-transparent py-3 pl-3 pr-24 text-xs sm:text-sm text-white placeholder:text-slate-500 focus:outline-none font-mono"
          />
          <button
            type="submit"
            disabled={isLoading}
            className="absolute right-1.5 top-1/2 -translate-y-1/2 px-4 py-1.5 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#BD9B58] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/20 active:scale-95 transition-all"
          >
            {isLoading ? <RefreshCw className="w-3.5 h-3.5 animate-spin" /> : <span>Cari</span>}
          </button>
        </div>
      </form>
    </div>
  );
}
