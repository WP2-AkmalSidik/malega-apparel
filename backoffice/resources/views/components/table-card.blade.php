@props([
    'title' => null,
    'subtitle' => null,
    'count' => null,
])

<!-- Outer Luxury Card Frame (Layer 1) -->
<div class="relative rounded-3xl bg-[#0B132B] border border-[#CBAC70]/20 p-4 sm:p-5 shadow-2xl shadow-black/40 overflow-hidden space-y-4">
    <!-- Top Gold Stitch Accent -->
    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-75"></div>

    <!-- Header Row: Title & Subtitle on Left, Primary Actions on Right -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <!-- Title & Subtitle -->
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="font-display text-lg sm:text-xl text-slate-100 tracking-tight">
                    {{ $title }}
                </h2>
                @if(isset($count))
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-[#CBAC70]/10 text-[#CBAC70] border border-[#CBAC70]/30">
                        {{ $count }} Total
                    </span>
                @endif
            </div>
            @if($subtitle)
                <p class="text-xs text-slate-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        <!-- Primary Actions (Create Buttons, etc.) -->
        @if(isset($actions))
            <div class="flex items-center gap-2 shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>

    <!-- Dedicated Full-Width Filter & Search Toolbar (Single Clean Horizontal Row) -->
    @if(isset($controls))
        <div class="flex flex-wrap lg:flex-nowrap items-center justify-between gap-2.5 pt-2 border-t border-white/[0.04]">
            {{ $controls }}
        </div>
    @endif

    <!-- Inner Elevated Card Container for Table (Layer 2) -->
    <div class="rounded-2xl bg-[#070C1A]/80 border border-slate-800/80 overflow-hidden shadow-inner">
        <div class="overflow-x-auto">
            {{ $slot }}
        </div>
    </div>

    <!-- Pagination & Footer Information Area -->
    @if(isset($pagination))
        <div class="pt-2">
            {{ $pagination }}
        </div>
    @endif
</div>
