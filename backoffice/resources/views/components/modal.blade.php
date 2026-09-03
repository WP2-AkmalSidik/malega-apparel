@props([
    'id' => 'modal',
    'title' => null,
    'subtitle' => null,
    'maxWidth' => '2xl', // sm, md, lg, xl, 2xl, 3xl, 4xl, full
])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    '5xl' => 'sm:max-w-5xl',
    '6xl' => 'sm:max-w-6xl',
    'full' => 'sm:max-w-full sm:m-4',
    default => 'sm:max-w-2xl',
};
@endphp

<div
    x-data="{
        isOpen: false,
        openModal() {
            this.isOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    x-on:open-modal-{{ $id }}.window="openModal()"
    x-on:close-modal-{{ $id }}.window="closeModal()"
    x-on:keydown.escape.window="closeModal()"
    class="relative z-50"
    style="display: none;"
    x-show="isOpen"
>
    <!-- Backdrop Overlay -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-250"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/80 backdrop-blur-xs transition-opacity"
        @click="closeModal()"
        aria-hidden="true"
    ></div>

    <!-- Modal Dialog Frame -->
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 lg:p-8 flex min-h-full items-center justify-center">
        <div
            x-show="isOpen"
            x-transition:enter="ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.stop
            class="relative w-full {{ $maxWidthClass }} transform overflow-hidden rounded-3xl bg-[#0B132B] border border-[#CBAC70]/20 shadow-2xl transition-all"
        >
            <!-- Gold Accent Stitch Strip at Top -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-80"></div>

            <!-- Modal Header -->
            <div class="px-6 pt-6 pb-4 flex items-start justify-between border-b border-white/5">
                <div>
                    @if(isset($header))
                        {{ $header }}
                    @else
                        <h2 class="font-display text-xl text-slate-100 tracking-tight">
                            {{ $title ?? 'Form Modal' }}
                        </h2>
                        @if($subtitle)
                            <p class="text-xs text-slate-400 mt-1">{{ $subtitle }}</p>
                        @endif
                    @endif
                </div>

                <!-- Close Button -->
                <button
                    type="button"
                    @click="closeModal()"
                    class="text-slate-400 hover:text-white p-1.5 rounded-xl hover:bg-white/5 transition-colors cursor-pointer shrink-0"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Slot) -->
            <div class="p-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
                {{ $slot }}
            </div>

            <!-- Modal Footer (Optional Slot) -->
            @if(isset($footer))
                <div class="px-6 py-4 bg-[#070C1A]/60 border-t border-white/5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
