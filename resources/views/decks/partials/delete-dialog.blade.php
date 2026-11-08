{{-- Confirmation dialog for deleting a note from history. Lives inside the history page's Alpine scope. --}}
<div
    x-show="deleteOpen"
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 sm:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="delete-dialog-title"
    aria-describedby="delete-dialog-body"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="deleteOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-ink/55 backdrop-blur-sm"
        @click="closeDelete()"
    ></div>

    <!-- Panel -->
    <div
        x-show="deleteOpen"
        x-transition:enter="transition ease-[cubic-bezier(0.34,1.45,0.64,1)] duration-400"
        x-transition:enter-start="opacity-0 translate-y-8 scale-90 -rotate-2"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100 rotate-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="delete-dialog relative w-full max-w-md academic-card shadow-pop-lg p-6 sm:p-7"
    >
        <button
            type="button"
            @click="closeDelete()"
            class="absolute top-3 right-3 rounded-lg p-1.5 text-slate-400 hover:text-ink hover:bg-slate-100 transition"
            aria-label="Tutup"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>

        <!-- Illustration: a note drifting into the bin, watched by Prof. Fahim -->
        <div class="flex items-end justify-center gap-3" aria-hidden="true">
            <div class="dd-fahim">
                @include('partials.fahim-avatar', ['class' => 'w-14 h-14'])
            </div>
            <svg viewBox="0 0 80 80" class="w-20 h-20 overflow-visible">
                <g class="dd-sheet">
                    <rect x="27" y="2" width="26" height="32" rx="4" fill="#ffffff" stroke="#2a1217" stroke-width="2.5"/>
                    <path d="M32 11h16M32 17h16M32 23h10" stroke="#d5c6b8" stroke-width="2.5" stroke-linecap="round"/>
                </g>
                <g class="dd-bin-lid">
                    <rect x="14" y="34" width="52" height="7" rx="3.5" fill="#bf4a35" stroke="#2a1217" stroke-width="2.5"/>
                    <rect x="32" y="29" width="16" height="6" rx="2.5" fill="#bf4a35" stroke="#2a1217" stroke-width="2.5"/>
                </g>
                <path d="M18 42h44l-4 32a4 4 0 0 1-4 3.5H26a4 4 0 0 1-4-3.5z" fill="#fbf4ea" stroke="#2a1217" stroke-width="2.5" stroke-linejoin="round"/>
                <path d="M32 50v19M40 50v19M48 50v19" stroke="#2a1217" stroke-width="2.5" stroke-linecap="round" opacity="0.35"/>
            </svg>
        </div>

        <h2 id="delete-dialog-title" class="mt-5 text-center text-2xl font-extrabold text-ink">Padam nota ini?</h2>

        <div id="delete-dialog-body" class="mt-4 space-y-4">
            <p class="verbatim-quote not-italic font-sans font-bold text-ink text-[15px] break-words" x-text="target.title"></p>

            <ul class="flex flex-wrap justify-center gap-1.5 text-xs font-bold text-ink">
                <li class="rounded-full border-2 border-ink bg-white px-2.5 py-0.5 tabular-nums"><span x-text="target.topics"></span> topik</li>
                <li class="rounded-full border-2 border-ink bg-white px-2.5 py-0.5 tabular-nums"><span x-text="target.questions"></span> soalan</li>
                <li class="rounded-full border-2 border-ink bg-white px-2.5 py-0.5 tabular-nums"><span x-text="target.flashcards"></span> kad imbas</li>
                <li class="rounded-full border-2 border-ink bg-white px-2.5 py-0.5 tabular-nums"><span x-text="target.sessions"></span> sesi latihan</li>
            </ul>

            <p class="text-center text-sm text-slate-600 leading-relaxed">
                Semua di atas, termasuk laporan guru dan log ejen AI, akan dipadam bersama nota ini.
                <strong class="text-ink">Tindakan ini tidak boleh dibatalkan.</strong>
            </p>
        </div>

        <form method="POST" :action="target.url" class="mt-6 flex flex-col-reverse sm:flex-row gap-2.5" @submit="deleting = true">
            @csrf
            @method('DELETE')
            @if(($search ?? '') !== '')
                <input type="hidden" name="q" value="{{ $search }}">
            @endif

            <button
                type="button"
                x-ref="cancelDelete"
                @click="closeDelete()"
                :disabled="deleting"
                class="btn-pop flex-1 bg-white text-ink px-4 py-2.5"
            >
                Batal, simpan nota
            </button>
            <button
                type="submit"
                :disabled="deleting"
                class="delete-confirm btn-pop flex-1 bg-tomato text-white px-4 py-2.5"
            >
                <span x-show="!deleting" class="inline-flex items-center gap-2">
                    @include('partials.trash-icon', ['class' => 'w-4 h-4'])
                    Ya, padam nota
                </span>
                <span x-show="deleting" class="inline-flex items-center gap-2" style="display: none;">
                    <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    Memadam…
                </span>
            </button>
        </form>
    </div>
</div>
