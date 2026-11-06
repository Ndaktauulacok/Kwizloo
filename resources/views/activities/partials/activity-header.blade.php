<div class="flex items-center justify-between gap-3 pb-5 mb-6 border-b-2 border-dashed border-slate-300">
    <div class="flex items-center gap-3 min-w-0">
        <a
            href="{{ route('decks.show', $deck) }}"
            class="btn-pop bg-white text-ink p-2 rounded-xl shrink-0"
            title="Keluar ke Hab Pembelajaran"
            aria-label="Keluar ke Hab Pembelajaran"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6-6 6 6 6"/></svg>
        </a>
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-ink bg-sun border-2 border-ink px-2 py-0.5 rounded-md">{{ $activityMeta['name'] }}</span>
                @if(isset($topic))
                    <span class="text-xs text-slate-600 font-semibold truncate">Topik: {{ $topic->name }}</span>
                @endif
            </div>
            <h2 class="text-lg sm:text-xl font-extrabold text-ink mt-1 truncate">{{ $title ?? $activityMeta['name'] }}</h2>
        </div>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        <div x-show="combo > 1" class="px-2.5 py-1 rounded-lg bg-mint border-2 border-ink text-ink text-xs font-extrabold animate-wiggle" style="display: none;">
            Combo x<span x-text="combo"></span>
        </div>
        <div class="px-3 py-1 rounded-lg bg-ink text-sun text-sm font-extrabold tabular-nums">
            <span x-text="score">0</span> <span class="text-paper/60 text-xs font-bold">mata</span>
        </div>
    </div>
</div>
