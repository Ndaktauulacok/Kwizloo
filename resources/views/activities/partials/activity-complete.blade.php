<div 
    x-show="completed" 
    class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
    style="display: none;"
    x-transition
>
    <div class="academic-card max-w-md w-full bg-white p-6 sm:p-8 text-center space-y-6 shadow-2xl">
        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
            ✓
        </div>

        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sesi Selesai</span>
            <h3 class="text-2xl font-bold text-slate-900 mt-1">Tahniah atas Usaha Anda</h3>
            <p class="text-xs text-slate-500 mt-1">Rekod jawapan anda telah disimpan dan dianalisis oleh ejen pedagogi.</p>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-3 gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
            <div>
                <span class="text-[10px] uppercase text-slate-400 font-semibold block">Mata</span>
                <span class="text-base font-bold font-mono text-slate-800" x-text="score">0</span>
            </div>
            <div>
                <span class="text-[10px] uppercase text-slate-400 font-semibold block">Ketepatan</span>
                <span class="text-base font-bold font-mono text-slate-800" x-text="accuracy + '%'">0%</span>
            </div>
            <div>
                <span class="text-[10px] uppercase text-slate-400 font-semibold block">Combo</span>
                <span class="text-base font-bold font-mono text-slate-800" x-text="'x' + bestCombo">x0</span>
            </div>
        </div>

        <!-- Adaptive Agent Next Recommendation -->
        <div x-show="nextMission" class="text-left p-3.5 bg-indigo-50/70 border border-indigo-200/80 rounded-xl text-xs space-y-1">
            <div class="font-bold text-indigo-900 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                <span>Langkah Seterusnya: <span x-text="nextMission?.activity_name"></span></span>
            </div>
            <p class="text-indigo-800/90 leading-relaxed" x-text="nextMission?.rationale"></p>
        </div>

        <!-- Actions -->
        <div class="space-y-2 pt-2">
            <template x-if="nextMission">
                <a 
                    :href="'{{ url('decks/' . $deck->id . '/activities') }}/' + nextMission.activity + (nextMission.topic_id ? '?topic_id=' + nextMission.topic_id : '')" 
                    class="w-full block py-2.5 px-4 rounded-xl bg-indigo-900 hover:bg-indigo-800 text-white text-xs font-semibold shadow-xs transition"
                >
                    Teruskan ke Misi Disyorkan
                </a>
            </template>
            <button 
                type="button" 
                @click="location.reload()" 
                class="w-full py-2.5 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition"
            >
                Ulang Aktiviti Ini
            </button>
            <a 
                href="{{ route('decks.show', $deck) }}" 
                class="w-full block py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition"
            >
                Kembali ke Hab Pembelajaran
            </a>
        </div>
    </div>
</div>
