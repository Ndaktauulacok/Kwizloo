<div
    x-show="completed"
    class="fixed inset-0 z-50 bg-ink/50 backdrop-blur-xs flex items-center justify-center p-4"
    style="display: none;"
    x-transition
>
    <div class="academic-card shadow-pop-lg max-w-md w-full p-6 sm:p-8 text-center space-y-6 relative" x-transition.scale.90>
        <div class="mx-auto -mt-14 w-20 h-20 rounded-2xl bg-sun border-2 border-ink shadow-pop flex items-center justify-center -rotate-6">
            <svg class="w-10 h-10 text-ink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-4v4M7 4h10v5a5 5 0 0 1-10 0V4zM7 6H4v1a3 3 0 0 0 3 3m10-4h3v1a3 3 0 0 1-3 3"/></svg>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Aktiviti selesai</span>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-ink mt-1"
                x-text="accuracy >= 80 ? 'Cemerlang! Anda menguasai topik ini.' : (accuracy >= 50 ? 'Bagus, anda semakin hampir.' : 'Usaha yang baik. Cuba sekali lagi.')">Tahniah atas usaha anda</h3>
            <p class="text-sm text-slate-500 mt-1.5">Keputusan anda telah disimpan untuk analisis prestasi.</p>
        </div>

        <div class="grid grid-cols-3 gap-2.5">
            <div class="p-3 rounded-xl bg-sun/40 border-2 border-ink">
                <span class="text-[10px] uppercase text-ink/70 font-bold block">Mata</span>
                <span class="text-xl font-extrabold text-ink tabular-nums" x-text="score">0</span>
            </div>
            <div class="p-3 rounded-xl bg-mint/40 border-2 border-ink">
                <span class="text-[10px] uppercase text-ink/70 font-bold block">Ketepatan</span>
                <span class="text-xl font-extrabold text-ink tabular-nums" x-text="accuracy + '%'">0%</span>
            </div>
            <div class="p-3 rounded-xl bg-bubblegum/40 border-2 border-ink">
                <span class="text-[10px] uppercase text-ink/70 font-bold block">Rentetan</span>
                <span class="text-xl font-extrabold text-ink tabular-nums" x-text="'x' + bestCombo">x0</span>
            </div>
        </div>

        <div x-show="nextMission" class="text-left p-4 bg-indigo-50 border-2 border-indigo-600 rounded-xl text-sm space-y-1">
            <div class="font-extrabold text-indigo-900">
                Cadangan seterusnya: <span x-text="nextMission?.activity_name"></span>
            </div>
            <p class="text-indigo-900/80 leading-relaxed text-[13px]" x-text="nextMission?.rationale"></p>
        </div>

        <div class="space-y-2.5">
            <template x-if="nextMission">
                <a
                    :href="'{{ url('decks/' . $deck->id . '/activities') }}/' + nextMission.activity + (nextMission.topic_id ? '?topic_id=' + nextMission.topic_id : '')"
                    class="btn-pop w-full bg-indigo-600 text-white py-3"
                >
                    Teruskan ke aktiviti disyorkan
                </a>
            </template>
            <button type="button" @click="location.reload()" class="btn-pop w-full bg-white text-ink py-3">
                Ulang aktiviti ini
            </button>
            <a href="{{ route('decks.show', $deck) }}" class="w-full block py-2 text-sm font-semibold text-slate-500 hover:text-ink transition">
                Kembali ke Hab Pembelajaran
            </a>
        </div>
    </div>
</div>
