@props([
    'id' => 'confirmation-modal',
    'title' => 'Konfirmasi Tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Konfirmasi',
    'cancelText' => 'Batal',
    'type' => 'danger', // danger, warning, gold, primary
    'icon' => null, // logout, delete, warning, info
])

@php
$iconPreset = match ($icon ?? $type) {
    'logout' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />',
    'delete', 'danger' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
    default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />',
};

$iconBgClass = match ($type) {
    'danger' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
    'warning' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
    'gold' => 'bg-[#CBAC70]/10 text-[#CBAC70] border border-[#CBAC70]/30',
    default => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
};

$confirmBtnClass = match ($type) {
    'danger' => 'bg-rose-600 hover:bg-rose-500 text-white focus:ring-rose-500 shadow-rose-950/30',
    'warning' => 'bg-amber-600 hover:bg-amber-500 text-white focus:ring-amber-500 shadow-amber-950/30',
    'gold' => 'bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold focus:ring-[#CBAC70] shadow-[#CBAC70]/20',
    default => 'bg-[#0B132B] hover:bg-[#121B3D] text-white border border-[#CBAC70]/40 hover:border-[#CBAC70] focus:ring-[#CBAC70]',
};
@endphp

<div
    x-data="{
        isOpen: false,
        dynamicTitle: '{{ $title }}',
        dynamicMessage: '{{ $message }}',
        dynamicData: null,
        openModal(detail) {
            if (typeof detail === 'object' && detail !== null) {
                if (detail.title) this.dynamicTitle = detail.title;
                if (detail.message) this.dynamicMessage = detail.message;
                if (detail.data) this.dynamicData = detail.data;
            }
            this.isOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    x-on:open-confirmation-{{ $id }}.window="openModal($event.detail)"
    x-on:close-confirmation-{{ $id }}.window="closeModal()"
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
        class="fixed inset-0 bg-black/75 backdrop-blur-xs transition-opacity"
        @click="closeModal()"
        aria-hidden="true"
    ></div>

    <!-- Modal Dialog Window -->
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 flex min-h-full items-center justify-center">
        <div
            x-show="isOpen"
            x-transition:enter="ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.stop
            class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-[#0B132B] border border-slate-800 p-6 text-left shadow-2xl transition-all"
        >
            <!-- Top Accent Stitch Line -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-60"></div>

            <div class="flex items-start gap-4">
                <!-- Icon Badge -->
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 shadow-inner {{ $iconBgClass }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $iconPreset !!}
                    </svg>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 class="text-base font-semibold text-slate-100 leading-6 tracking-tight" x-text="dynamicTitle">
                        {{ $title }}
                    </h3>
                    <p class="mt-2 text-xs text-slate-400 leading-relaxed" x-text="dynamicMessage">
                        {{ $message }}
                    </p>
                </div>
            </div>

            <!-- Custom Content Slot (if any) -->
            @if(isset($content))
                <div class="mt-4">
                    {{ $content }}
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                <button
                    type="button"
                    @click="closeModal()"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-500"
                >
                    {{ $cancelText }}
                </button>

                @if(isset($action))
                    {{ $action }}
                @else
                    <button
                        type="button"
                        @click="$dispatch('confirmed-{{ $id }}', dynamicData); closeModal()"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold shadow-md transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#0B132B] {{ $confirmBtnClass }}"
                    >
                        {{ $confirmText }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
