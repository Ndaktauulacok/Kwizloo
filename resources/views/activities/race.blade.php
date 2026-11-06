@extends('layouts.app', ['title' => 'Penanda Aras Adaptif (Perlumbaan Bot)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-3xl mx-auto py-2"
    x-data="raceActivity({
        verifyUrl: '{{ route('decks.verify-answer', $deck) }}',
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        questions: {{ Js::from($questions) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Penanda Aras Adaptif: Bandingan Kelajuan & Ketepatan'])

    <!-- Track Visualizer -->
    <div class="academic-card p-5 bg-white border border-slate-200 mb-6 space-y-4">
        <!-- Student Track -->
        <div class="space-y-1">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <span>Anda</span>
                </span>
                <span class="font-mono text-slate-500" x-text="playerProgress + '%'">0%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden relative">
                <div 
                    class="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                    :style="'width: ' + playerProgress + '%'"
                ></div>
            </div>
        </div>

        <!-- Bot Track -->
        <div class="space-y-1">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    <span>Bot Penanda Aras (75% Ketepatan)</span>
                </span>
                <span class="font-mono text-slate-400" x-text="botProgress + '%'">0%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden relative">
                <div 
                    class="bg-slate-400 h-3 rounded-full transition-all duration-500"
                    :style="'width: ' + botProgress + '%'"
                ></div>
            </div>
        </div>
    </div>

    <!-- Active Question Card -->
    <template x-if="currentQ && !completed">
        <div class="academic-card p-6 sm:p-8 bg-white border border-slate-200 space-y-6">
            <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-100 pb-3">
                <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded font-mono">Soalan <span x-text="currentIndex + 1"></span></span>
                <span>Jawab pantas untuk menewaskan penanda aras bot</span>
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

            <!-- Feedback -->
            <template x-if="answered">
                <div 
                    class="p-4 rounded-xl border text-xs space-y-1.5"
                    :class="feedbackData?.is_correct ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'"
                >
                    <div class="font-bold text-sm" x-text="feedbackData?.is_correct ? '✓ Tepat! Anda memecut ke hadapan.' : '✗ Kurang tepat. Bot sedang merapatkan jurang.'"></div>
                    <p x-text="feedbackData?.explanation"></p>
                    <div class="verbatim-quote bg-white/70">
                        "<span x-text="feedbackData?.quote"></span>"
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="button" @click="nextStep()" class="px-5 py-2 rounded-lg bg-indigo-900 text-white font-semibold text-xs hover:bg-indigo-800 transition">
                            Teruskan Memecut &rarr;
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </template>

    @include('activities.partials.activity-complete')
</div>

<script>
function raceActivity(config) {
    return {
        questions: config.questions || [],
        currentIndex: 0,
        currentQ: null,
        playerProgress: 0,
        botProgress: 0,
        botTimer: null,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        answered: false,
        selectedIdx: null,
        feedbackData: null,
        completed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            if (this.questions.length > 0) {
                this.currentQ = this.questions[0];
            }

            this.startBot();

            window.addEventListener('keydown', (e) => {
                if (this.answered || this.completed) return;
                if (['1', '2', '3', '4'].includes(e.key)) {
                    this.submitAnswer(parseInt(e.key) - 1);
                }
            });
        },

        startBot() {
            this.botTimer = setInterval(() => {
                if (this.completed) return;
                // Bot moves forward smoothly, approx 2% every 1.2 seconds (~60 seconds to finish)
                this.botProgress = Math.min(100, this.botProgress + 2);
                if (this.botProgress >= 100 && this.playerProgress < 100) {
                    this.finishSession();
                }
            }, 1200);
        },

        getOptionClass(idx) {
            if (!this.answered) return 'border-slate-200 bg-white hover:border-indigo-400 hover:bg-slate-50';
            if (idx === this.feedbackData?.correct_index) return 'border-emerald-500 bg-emerald-50 text-emerald-950 font-semibold';
            if (idx === this.selectedIdx) return 'border-rose-400 bg-rose-50 text-rose-950';
            return 'border-slate-200 bg-slate-50 opacity-40';
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

                this.feedbackData = res;

                if (res.is_correct) {
                    window.AcademicAudio.correct();
                    this.rightCount++;
                    this.combo++;
                    this.bestCombo = Math.max(this.bestCombo, this.combo);
                    this.score += 20 + (this.combo * 2);
                    this.playerProgress = Math.min(100, this.playerProgress + 15);
                } else {
                    window.AcademicAudio.incorrect();
                    this.wrongCount++;
                    this.combo = 0;
                    // small progress stall
                }
            } catch (e) {
                this.answered = false;
            }
        },

        nextStep() {
            if (this.playerProgress >= 100) {
                this.finishSession();
                return;
            }

            this.currentIndex = (this.currentIndex + 1) % this.questions.length;
            this.currentQ = this.questions[this.currentIndex];
            this.answered = false;
            this.selectedIdx = null;
            this.feedbackData = null;
        },

        async finishSession() {
            this.completed = true;
            clearInterval(this.botTimer);
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'race',
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
