<div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
    <div class="flex items-center space-x-3">
        <a 
            href="{{ route('decks.show', $deck) }}" 
            class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition shadow-2xs"
            title="Keluar ke Hab Pembelajaran"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">{{ $activityMeta['name'] }}</span>
                @if(isset($topic))
                    <span class="text-xs text-slate-500 font-medium">Topik: {{ $topic->name }}</span>
                @endif
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-0.5">{{ $title ?? $activityMeta['name'] }}</h2>
        </div>
    </div>

    <div class="flex items-center space-x-2">
        <div x-show="combo > 1" class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold font-mono" style="display: none;">
            Combo x<span x-text="combo"></span>
        </div>
        <div class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-mono font-bold">
            Mata: <span x-text="score">0</span>
        </div>
    </div>
</div>
