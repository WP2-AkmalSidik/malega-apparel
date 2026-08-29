@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-2 px-1 text-xs">
        <!-- Results Counter Information -->
        <div class="text-slate-400 font-mono text-[11px]">
            Menampilkan
            <span class="font-semibold text-[#CBAC70]">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-semibold text-[#CBAC70]">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-slate-200">{{ $paginator->total() }}</span>
            entri data
        </div>

        <!-- Navigation Controls -->
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900/40 text-slate-600 cursor-not-allowed text-xs font-semibold select-none">
                    &larr; Sebelumnya
                </span>
            @else
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="px-3 py-1.5 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer text-xs font-semibold shadow-xs"
                >
                    &larr; Sebelumnya
                </button>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-2.5 py-1.5 text-slate-600 text-xs font-mono select-none">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/20 select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    wire:loading.attr="disabled"
                                    class="px-3 py-1.5 rounded-xl border border-slate-800/80 bg-slate-900/40 hover:bg-slate-800 text-slate-400 hover:text-white transition-colors cursor-pointer text-xs font-semibold"
                                >
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="px-3 py-1.5 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer text-xs font-semibold shadow-xs"
                >
                    Selanjutnya &rarr;
                </button>
            @else
                <span class="px-3 py-1.5 rounded-xl border border-slate-800 bg-slate-900/40 text-slate-600 cursor-not-allowed text-xs font-semibold select-none">
                    Selanjutnya &rarr;
                </span>
            @endif
        </nav>
    </div>
@endif
