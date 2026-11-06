@extends('layouts.app', ['title' => 'Ujian Pantas (Blitz 60)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-2xl mx-auto py-2"
    x-data="blitzActivity({
        verifyUrl: '{{ route('decks.verify-answer', $deck) }}',
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        questions: {{ Js::from($questions) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Ujian Pantas: Blitz 60 Saat'])

    <!-- 60-Second Timer Bar -->
    <div class="academic-card p-4 bg-white border border-slate-200 mb-6">
        <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5 font-medium">
            <span>Masa Berbaki:</span>
            <span class="font-mono font-bold text-slate-900" x-text="timeLeft + 's'">60s</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
            <div 
                class="h-2 rounded-full transition-all duration-200"
                :class="timeLeft <= 10 ? 'bg-rose-500' : 'bg-indigo-600'"
                :style="'width: ' + ((timeLeft / 60) * 100) + '%'"
            ></div>
        </div>
    </div>

    <!-- Active Question Card -->
    <template x-if="currentQ && !completed">
        <div class="academic-card p-6 sm:p-8 bg-white border border-slate-200 space-y-6">
            <div class="text-xs text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded inline-block font-mono">
                Soalan <span x-text="currentIndex + 1"></span>
            </div>

            <h3 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug" x-text="currentQ.question"></h3>

            <div class="grid grid-cols-1 gap-2.5">
                <template x-for="(opt, idx) in currentQ.options" :key="idx">
                    <button 
                        type="button" 
                        @click="submitAnswer(idx)"
                        :disabled="answered"
                        class="p-3.5 rounded-xl border text-left text-sm font-medium transition flex items-center justify-between"
                        :class="getOptionClass(idx)"
                    >
                        <span class="text-slate-800" x-text="opt"></span>
                        <span class="text-xs font-mono text-slate-400" x-text="'[' + (idx + 1) + ']'"></span>
                    </button>
                </template>
            </div>
        </div>
    </template>

    @include('activities.partials.activity-complete')
</div>

<script>
function blitzActivity(config) {
    return {
        questions: config.questions || [],
        currentIndex: 0,
        currentQ: null,
        timeLeft: 60,
        timer: null,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        answered: false,
        selectedIdx: null,
        correctIdx: null,
        completed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            if (this.questions.length > 0) {
                this.currentQ = this.questions[0];
            }
            this.startTimer();

            window.addEventListener('keydown', (e) => {
                if (this.answered || this.completed) return;
                if (['1', '2', '3', '4'].includes(e.key)) {
                    this.submitAnswer(parseInt(e.key) - 1);
                }
            });
        },

        startTimer() {
            this.timer = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(this.timer);
                    this.finishSession();
                }
            }, 1000);
        },

        getOptionClass(idx) {
            if (!this.answered) {
                return 'border-slate-200 bg-white hover:border-indigo-400 hover:bg-slate-50';
            }
            if (idx === this.correctIdx) {
                return 'border-emerald-500 bg-emerald-50 text-emerald-950 font-semibold';
            }
            if (idx === this.selectedIdx) {
                return 'border-rose-400 bg-rose-50 text-rose-950';
            }
            return 'border-slate-200 bg-slate-50 opacity-50';
        },

        async submitAnswer(idx) {
            if (this.answered || !this.currentQ || this.completed) return;
            this.answered = true;
            this.selectedIdx = idx;

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
                    this.score += 10 + Math.min(this.combo * 2, 20);
                } else {
                    window.AcademicAudio.incorrect();
                    this.wrongCount++;
                    this.combo = 0;
                    this.timeLeft = Math.max(0, this.timeLeft - 2); // 2s penalty
                }

                setTimeout(() => this.nextQuestion(), 400);
            } catch (e) {
                this.answered = false;
            }
        },

        nextQuestion() {
            if (this.completed) return;
            this.currentIndex++;
            if (this.currentIndex >= this.questions.length) {
                this.currentIndex = 0; // loop questions during 60s
            }
            this.currentQ = this.questions[this.currentIndex];
            this.answered = false;
            this.selectedIdx = null;
            this.correctIdx = null;
        },

        async finishSession() {
            this.completed = true;
            clearInterval(this.timer);
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'blitz',
                    topic_id: config.topicId,
                    score: this.score,
                    right_count: this.rightCount,
                    wrong_count: this.wrongCount,
                    best_combo: this.bestCombo,
                    duration_seconds: 60 - this.timeLeft
                });
                this.nextMission = res.next_mission;
            } catch (e) {}
        }
    };
}
</script>
@endsection
