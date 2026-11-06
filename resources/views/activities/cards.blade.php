@extends('layouts.app', ['title' => 'Flashcard Berjarak (Kad Imbas)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-2xl mx-auto py-2"
    x-data="cardsActivity({
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        flashcards: {{ Js::from($flashcards) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Flashcard Berjarak: Ulang Kaji Aktif'])

    <!-- Progress Meter -->
    <div class="academic-card p-4 bg-white border border-slate-200 mb-6 flex items-center justify-between">
        <div class="space-y-1 w-full max-w-xs">
            <div class="flex justify-between text-xs text-slate-500 font-medium">
                <span>Kemajuan Penguasaan:</span>
                <span class="font-mono font-bold text-slate-800" x-text="masteredCount + ' / ' + totalInitial + ' kad'"></span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="'width: ' + ((masteredCount / Math.max(1, totalInitial)) * 100) + '%'"></div>
            </div>
        </div>
        <span class="text-xs text-slate-400">Tekan [Ruang] untuk pusing kad</span>
    </div>

    <!-- Active 3D Flashcard -->
    <template x-if="currentCard && !completed">
        <div class="space-y-6">
            <div 
                @click="flipped = !flipped"
                class="academic-card p-8 sm:p-12 bg-white border border-slate-200 min-h-[260px] sm:min-h-[300px] flex flex-col justify-center items-center text-center cursor-pointer shadow-sm hover:border-indigo-400 transition select-none relative"
            >
                <span class="absolute top-4 left-4 text-[10px] font-semibold uppercase tracking-wider text-slate-400" x-text="flipped ? 'Definisi & Petikan' : 'Konsep / Istilah'"></span>
                <span class="absolute top-4 right-4 text-xs text-slate-400">Pusing &circlearrowright;</span>

                <!-- Front Side -->
                <div x-show="!flipped" class="space-y-3">
                    <span class="text-xs uppercase text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded font-semibold" x-text="currentCard.topic?.name || 'Topik'"></span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 leading-snug" x-text="currentCard.front"></h3>
                </div>

                <!-- Back Side -->
                <div x-show="flipped" style="display: none;" class="space-y-4 max-w-lg">
                    <p class="text-base sm:text-lg text-slate-800 font-medium leading-relaxed" x-text="currentCard.back"></p>
                    <template x-if="currentCard.quote">
                        <div class="verbatim-quote text-left">
                            "<span x-text="currentCard.quote"></span>"
                        </div>
                    </template>
                </div>
            </div>

            <!-- Self-Assessment Action Controls -->
            <div class="grid grid-cols-2 gap-4">
                <button 
                    type="button" 
                    @click="markCard(false)"
                    class="py-3.5 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs sm:text-sm transition shadow-2xs flex items-center justify-center space-x-2"
                >
                    <span class="text-rose-500">↺</span>
                    <span>Perlu Ulang Lagi</span>
                </button>

                <button 
                    type="button" 
                    @click="markCard(true)"
                    class="py-3.5 px-4 rounded-xl bg-indigo-900 hover:bg-indigo-800 text-white font-bold text-xs sm:text-sm transition shadow-xs flex items-center justify-center space-x-2"
                >
                    <span>✓ Sudah Faham</span>
                </button>
            </div>
        </div>
    </template>

    @include('activities.partials.activity-complete')
</div>

<script>
function cardsActivity(config) {
    return {
        queue: [],
        currentCard: null,
        flipped: false,
        totalInitial: 0,
        masteredCount: 0,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        completed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            this.queue = [...(config.flashcards || [])];
            this.totalInitial = this.queue.length;
            if (this.queue.length > 0) {
                this.currentCard = this.queue[0];
            } else {
                this.completed = true;
            }

            window.addEventListener('keydown', (e) => {
                if (e.code === 'Space' && !this.completed) {
                    e.preventDefault();
                    this.flipped = !this.flipped;
                }
            });
        },

        markCard(known) {
            if (!this.currentCard || this.completed) return;
            const card = this.queue.shift();

            if (known) {
                window.AcademicAudio.correct();
                this.masteredCount++;
                this.rightCount++;
                this.combo++;
                this.bestCombo = Math.max(this.bestCombo, this.combo);
                this.score += 10 + (this.combo * 2);
            } else {
                window.AcademicAudio.incorrect();
                this.wrongCount++;
                this.combo = 0;
                this.queue.push(card); // Re-queue for spaced recall
            }

            this.flipped = false;

            if (this.queue.length === 0) {
                this.finishSession();
            } else {
                this.currentCard = this.queue[0];
            }
        },

        async finishSession() {
            this.completed = true;
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'cards',
                    topic_id: config.topicId,
                    score: this.score,
                    right_count: this.rightCount,
                    wrong_count: this.wrongCount,
                    best_combo: this.bestCombo,
                    duration_seconds: 40
                });
                this.nextMission = res.next_mission;
            } catch (e) {}
        }
    };
}
</script>
@endsection
