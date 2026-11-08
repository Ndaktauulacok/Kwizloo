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
        <!-- AI Hint trigger if activity exposes currentQ -->
        <template x-if="typeof currentQ !== 'undefined' && currentQ">
            <button
                type="button"
                @click="requestAiHint(currentQ.id)"
                class="px-2.5 py-1 rounded-lg bg-white border-2 border-ink text-ink text-xs font-bold hover:bg-sun transition flex items-center gap-1.5 shadow-pop-xs"
                title="Minta Petunjuk AI"
            >
                <svg class="w-3.5 h-3.5 text-tomato" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.477.859h4z"/></svg>
                <span class="hidden sm:inline">Petunjuk AI</span>
            </button>
        </template>

        <div x-show="combo > 1" class="px-2.5 py-1 rounded-lg bg-mint border-2 border-ink text-ink text-xs font-extrabold animate-wiggle" style="display: none;">
            Rentetan x<span x-text="combo"></span>
        </div>
        <div class="px-3 py-1 rounded-lg bg-ink text-sun text-sm font-extrabold tabular-nums">
            <span x-text="score">0</span> <span class="text-paper/60 text-xs font-bold">mata</span>
        </div>
    </div>
</div>

<!-- Modal Petunjuk AI Global -->
<div 
    x-data="{ 
        aiHintOpen: false, 
        aiHintText: '', 
        aiHintLoading: false,
        hintUrl: '{{ route('decks.ai-hint', $deck) }}'
    }"
    @open-ai-hint.window="
        aiHintOpen = true; 
        aiHintText = $event.detail.hint || ''; 
        aiHintLoading = $event.detail.loading || false;
    "
    @keydown.escape.window="aiHintOpen = false"
    x-show="aiHintOpen"
    x-transition.opacity
    role="dialog"
    aria-modal="true"
    aria-label="Bimbingan Petunjuk AI"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/60 backdrop-blur-xs"
    style="display: none;"
>
    <div 
        @click.outside="aiHintOpen = false"
        class="card max-w-md w-full bg-white border-3 border-ink rounded-3xl p-6 shadow-pop text-left relative"
    >
        <div class="flex items-center justify-between pb-3 border-b-2 border-dashed border-slate-200">
            <div class="flex items-center gap-2">
                @include('partials.fahim-avatar', ['class' => 'w-8 h-8'])
                <h4 class="font-display font-extrabold text-base text-ink">Bimbingan Petunjuk AI</h4>
            </div>
            <button type="button" @click="aiHintOpen = false" aria-label="Tutup" class="text-slate-400 hover:text-ink font-bold text-lg leading-none">&times;</button>
        </div>

        <div class="py-4 text-sm text-slate-700 leading-relaxed min-h-[70px] flex items-center">
            <template x-if="aiHintLoading">
                <div class="flex items-center gap-2 text-slate-500 font-semibold text-xs">
                    <svg class="animate-spin h-4 w-4 text-ink" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Menghasilkan petunjuk berasaskan nota...
                </div>
            </template>
            <template x-if="!aiHintLoading">
                <p class="font-medium italic text-ink" x-text="aiHintText"></p>
            </template>
        </div>

        <div class="flex justify-end pt-2">
            <button 
                type="button" 
                @click="aiHintOpen = false"
                class="px-4 py-1.5 rounded-xl bg-ink text-white font-bold text-xs hover:bg-slate-800 transition shadow-pop-xs"
            >
                Faham, Terima Kasih
            </button>
        </div>
    </div>
</div>

<script>
window.requestAiHint = async function(paramQId) {
    const questionId = paramQId || window._currentQuestionId || (typeof currentQ !== 'undefined' && currentQ ? currentQ.id : null);
    if (!questionId) return;

    window.dispatchEvent(new CustomEvent('open-ai-hint', { detail: { loading: true, hint: '' } }));

    try {
        const res = await fetch("{{ route('decks.ai-hint', $deck) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ question_id: questionId })
        });
        const data = await res.json().catch(() => ({}));
        const hint = res.ok
            ? (data.hint || 'Tiada petunjuk ditemui.')
            : (res.status === 429 ? 'Anda terlalu kerap meminta petunjuk. Cuba lagi sebentar nanti.' : (data.message || 'Petunjuk tidak dapat dimuatkan buat masa ini.'));
        window.dispatchEvent(new CustomEvent('open-ai-hint', { detail: { loading: false, hint } }));
    } catch (e) {
        window.dispatchEvent(new CustomEvent('open-ai-hint', { detail: { loading: false, hint: 'Gagal memuat turun petunjuk AI.' } }));
    }
};
</script>
