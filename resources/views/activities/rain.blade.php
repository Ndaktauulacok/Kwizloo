@extends('layouts.app', ['title' => 'Sintesis Menurun (Hujan Jawapan)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-3xl mx-auto py-2"
    x-data="rainActivity({
        verifyUrl: '{{ route('decks.verify-answer', $deck) }}',
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        questions: {{ Js::from($questions) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Sintesis Menurun: Tangkap Konsep Tepat'])

    <!-- Lives & Progress -->
    <div class="academic-card p-4 bg-white border border-slate-200 mb-6 flex items-center justify-between">
        <div class="text-xs font-semibold text-slate-500">
            Pusingan <span class="text-slate-900 font-mono" x-text="round + ' / 10'"></span>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs text-slate-400 font-semibold">Nyawa:</span>
            <span class="text-rose-600 font-bold font-mono text-sm tracking-wider" x-text="'♥ '.repeat(lives) + '♡ '.repeat(Math.max(0, 3 - lives))"></span>
        </div>
    </div>

    <!-- Active Question and Descending Drops -->
    <template x-if="currentQ && !completed">
        <div class="academic-card p-6 bg-white border border-slate-200 space-y-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 text-center" x-text="currentQ.question"></h3>

            <!-- Falling Area -->
            <div class="relative h-72 sm:h-80 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden">
                <template x-for="(opt, idx) in currentQ.options" :key="idx">
                    <button 
                        type="button"
                        @click="catchAnswer(idx)"
                        :disabled="answered"
                        class="absolute px-4 py-2.5 rounded-xl border text-xs sm:text-sm font-semibold shadow-xs transition cursor-pointer text-left max-w-[45%]"
                        :class="getOptionClass(idx)"
                        :style="'top: ' + dropPositions[idx] + '%; left: ' + (4 + (idx * 24)) + '%;'"
                    >
                        <span class="text-[10px] font-mono text-slate-400 block" x-text="'[' + (idx + 1) + ']'"></span>
                        <span class="leading-tight" x-text="opt"></span>
                    </button>
                </template>
            </div>
        </div>
    </template>

    @include('activities.partials.activity-complete')
</div>

<script>
function rainActivity(config) {
    return {
        questions: config.questions || [],
        currentIndex: 0,
        currentQ: null,
        round: 1,
        lives: 3,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        answered: false,
        selectedIdx: null,
        correctIdx: null,
        dropPositions: [0, 0, 0, 0],
        animInterval: null,
        completed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            if (this.questions.length > 0) {
                this.currentQ = this.questions[0];
                this.startDrop();
            }

            window.addEventListener('keydown', (e) => {
                if (this.answered || this.completed) return;
                if (['1', '2', '3', '4'].includes(e.key)) {
                    this.catchAnswer(parseInt(e.key) - 1);
                }
            });
        },

        startDrop() {
            this.dropPositions = [5, 12, 8, 15];
            clearInterval(this.animInterval);

            this.animInterval = setInterval(() => {
                if (this.answered || this.completed) return;
                let anyOffscreen = false;
                this.dropPositions = this.dropPositions.map(pos => {
                    const next = pos + 1.2;
                    if (next > 78) anyOffscreen = true;
                    return next;
                });

                if (anyOffscreen) {
                    this.handleTimeout();
                }
            }, 100);
        },

        getOptionClass(idx) {
            if (!this.answered) {
                return 'bg-white border-slate-300 text-slate-800 hover:border-indigo-500 hover:bg-indigo-50/30';
            }
            if (idx === this.correctIdx) {
                return 'bg-emerald-50 border-emerald-500 text-emerald-900 font-bold';
            }
            if (idx === this.selectedIdx) {
                return 'bg-rose-50 border-rose-400 text-rose-900';
            }
            return 'bg-slate-100 border-slate-200 text-slate-400 opacity-40';
        },

        async catchAnswer(idx) {
            if (this.answered || this.completed || !this.currentQ) return;
            this.answered = true;
            this.selectedIdx = idx;
            clearInterval(this.animInterval);

            try {
                const res = await window.secureFetch(config.verifyUrl, {
                    question_id: this.currentQ.id,
                    selected_index: idx
                });

                this.correctIdx = res.correct_index;

                if (res.is_correct) {
                    window.AcademicAudio.correct();
                    this.rightCount++;
                    this.combo++;
                    this.bestCombo = Math.max(this.bestCombo, this.combo);
                    this.score += 15 + (this.combo * 2);
                } else {
                    window.AcademicAudio.incorrect();
                    this.wrongCount++;
                    this.combo = 0;
                    this.lives--;
                }

                setTimeout(() => this.nextRound(), 600);
            } catch (e) {
                this.answered = false;
            }
        },

        handleTimeout() {
            clearInterval(this.animInterval);
            this.lives--;
            this.wrongCount++;
            this.combo = 0;
            window.AcademicAudio.incorrect();
            this.nextRound();
        },

        nextRound() {
            if (this.lives <= 0 || this.round >= 10) {
                this.finishSession();
                return;
            }

            this.round++;
            this.currentIndex = (this.currentIndex + 1) % this.questions.length;
            this.currentQ = this.questions[this.currentIndex];
            this.answered = false;
            this.selectedIdx = null;
            this.correctIdx = null;
            this.startDrop();
        },

        async finishSession() {
            this.completed = true;
            clearInterval(this.animInterval);
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'rain',
                    topic_id: config.topicId,
                    score: this.score,
                    right_count: this.rightCount,
                    wrong_count: this.wrongCount,
                    best_combo: this.bestCombo,
                    duration_seconds: 60
                });
                this.nextMission = res.next_mission;
            } catch (e) {}
        }
    };
}
</script>
@endsection
