@props([
    'title' => null,
    'subtitle' => null,
    'count' => null,
])

<!-- Outer Luxury Card Frame (Layer 1) -->
<div class="relative rounded-3xl bg-[#0B132B] border border-[#CBAC70]/20 p-5 sm:p-6 shadow-2xl shadow-black/40 overflow-hidden space-y-5">
    <!-- Top Gold Stitch Accent -->
    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-75"></div>

    <!-- Header & Action Controls Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Title & Subtitle -->
        <div>
            <div class="flex items-center gap-3">
                <h2 class="font-display text-xl sm:text-2xl text-slate-100 tracking-tight">
                    {{ $title }}
                </h2>
                @if(isset($count))
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-[#CBAC70]/10 text-[#CBAC70] border border-[#CBAC70]/30">
                        {{ $count }} Total
                    </span>
                @endif
            </div>
            @if($subtitle)
                <p class="text-xs text-slate-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        <!-- Filter & Action Controls -->
        @if(isset($controls) || isset($actions))
            <div class="flex flex-wrap items-center gap-3">
                @if(isset($controls))
                    {{ $controls }}
                @endif

                @if(isset($actions))
                    {{ $actions }}
                @endif
            </div>
        @endif
    </div>

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
