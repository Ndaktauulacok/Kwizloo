@extends('layouts.app', ['title' => 'Rekonstruksi Istilah (Teka Kata)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-2xl mx-auto py-2"
    x-data="wordActivity({
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        items: {{ Js::from($flashcards->map(fn($f) => ['term' => mb_strtoupper($f->front), 'clue' => $f->back, 'quote' => $f->quote])->filter(fn($i) => mb_strlen($i['term']) <= 20 && preg_match('/^[A-Z\s]+$/u', $i['term']))->values()) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Rekonstruksi Istilah: Lengkapkan Ejaan Kata Kunci'])

    <div class="academic-card p-4 bg-white border border-slate-200 mb-6 flex items-center justify-between">
        <span class="text-xs text-slate-500 font-semibold">Istilah <span class="font-mono text-slate-800" x-text="currentIndex + 1 + ' / ' + items.length"></span></span>
        <div class="flex items-center space-x-1.5">
            <span class="text-xs text-slate-400">Peluang:</span>
            <span class="font-mono text-xs text-indigo-700 font-bold" x-text="'● '.repeat(lives) + '○ '.repeat(Math.max(0, 6 - lives))"></span>
        </div>
    </div>

    <!-- Active Term Card -->
    <template x-if="currentItem && !completed">
        <div class="academic-card p-6 sm:p-8 bg-white border border-slate-200 space-y-6 text-center">
            <div class="space-y-1">
                <span class="text-xs uppercase font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded inline-block">Petunjuk Definisi:</span>
                <p class="text-base text-slate-800 font-medium max-w-lg mx-auto pt-2 leading-relaxed" x-text="currentItem.clue"></p>
            </div>

            <!-- Letter Slots -->
            <div class="flex flex-wrap items-center justify-center gap-2 py-4">
                <template x-for="(char, idx) in currentItem.term.split('')" :key="idx">
                    <div 
                        class="w-10 h-12 rounded-xl border flex items-center justify-center font-mono font-bold text-lg shadow-2xs"
                        :class="char === ' ' ? 'border-transparent bg-transparent w-4' : (guessedChars.includes(char) || lives <= 0 ? 'border-indigo-600 bg-indigo-50 text-indigo-900' : 'border-slate-300 bg-white text-transparent')"
                        x-text="char === ' ' ? ' ' : (guessedChars.includes(char) || lives <= 0 ? char : '_')"
                    ></div>
                </template>
            </div>

            <!-- Keyboard -->
            <div class="flex flex-wrap justify-center gap-1.5 max-w-lg mx-auto">
                <template x-for="k in 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')" :key="k">
                    <button 
                        type="button" 
                        @click="guessKey(k)"
                        :disabled="guessedChars.includes(k) || roundOver"
                        class="w-9 h-10 rounded-lg border text-xs font-mono font-bold transition"
                        :class="guessedChars.includes(k) ? 'border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed' : 'border-slate-300 bg-white hover:border-indigo-500 hover:bg-indigo-50/50 text-slate-800'"
                        x-text="k"
                    ></button>
                </template>
            </div>

            <!-- Round Result Banner -->
            <template x-if="roundOver">
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <p class="text-sm font-bold" :class="isWordWon() ? 'text-emerald-700' : 'text-rose-700'" x-text="isWordWon() ? '✓ Tepat Sekali!' : 'Peluang habis. Istilah sebenar ditunjukkan di atas.'"></p>
                    <button 
                        type="button" 
                        @click="nextWord()"
                        class="px-5 py-2 rounded-xl bg-indigo-900 text-white text-xs font-semibold hover:bg-indigo-800 transition"
                    >
                        Istilah Seterusnya &rarr;
                    </button>
                </div>
            </template>
        </div>
    </template>

    @include('activities.partials.activity-complete')
</div>

<script>
function wordActivity(config) {
    return {
        items: config.items || [],
        currentIndex: 0,
        currentItem: null,
        guessedChars: [],
        lives: 6,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        roundOver: false,
        completed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            if (this.items.length > 0) {
                this.currentItem = this.items[0];
            }

            window.addEventListener('keydown', (e) => {
                if (this.roundOver || this.completed) return;
                const key = e.key.toUpperCase();
                if (/^[A-Z]$/.test(key)) {
                    this.guessKey(key);
                }
            });
        },

        guessKey(char) {
            if (this.guessedChars.includes(char) || this.roundOver || !this.currentItem) return;
            this.guessedChars.push(char);

            if (this.currentItem.term.includes(char)) {
                window.AcademicAudio.correct();
                if (this.isWordWon()) {
                    this.roundOver = true;
                    this.rightCount++;
                    this.combo++;
                    this.bestCombo = Math.max(this.bestCombo, this.combo);
                    this.score += 25 + (this.combo * 2);
                }
            } else {
                window.AcademicAudio.incorrect();
                this.lives--;
                if (this.lives <= 0) {
                    this.roundOver = true;
                    this.wrongCount++;
                    this.combo = 0;
                }
            }
        },

        isWordWon() {
            if (!this.currentItem) return false;
            return this.currentItem.term.split('').every(c => c === ' ' || this.guessedChars.includes(c));
        },

        nextWord() {
            this.currentIndex++;
            if (this.currentIndex >= this.items.length) {
                this.finishSession();
                return;
            }
            this.currentItem = this.items[this.currentIndex];
            this.guessedChars = [];
            this.lives = 6;
            this.roundOver = false;
        },

        async finishSession() {
            this.completed = true;
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'word',
                    topic_id: config.topicId,
                    score: this.score,
                    right_count: this.rightCount,
                    wrong_count: this.wrongCount,
                    best_combo: this.bestCombo,
                    duration_seconds: 45
                });
                this.nextMission = res.next_mission;
            } catch (e) {}
        }
    };
}
</script>
@endsection
