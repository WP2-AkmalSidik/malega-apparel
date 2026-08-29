@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-[#0B132B]/80 border border-slate-800 rounded-2xl shadow-xl backdrop-blur-md overflow-hidden']) }}>
    @if(isset($header) || $title)
        <div class="px-6 py-5 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            @if(isset($header))
                {{ $header }}
            @else
                <div>
                    <h3 class="text-base font-semibold text-slate-100 tracking-tight">{{ $title }}</h3>
                    @if($subtitle)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif

            @if(isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 bg-[#080E21]/60 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
            {{ $footer }}
        </div>
    @endif
</div>
