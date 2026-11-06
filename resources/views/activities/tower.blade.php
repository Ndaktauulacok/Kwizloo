@extends('layouts.app', ['title' => 'Menara Kefahaman (Menara Ilmu)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-4xl mx-auto py-2"
    x-data="towerActivity({
        verifyUrl: '{{ route('decks.verify-answer', $deck) }}',
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        questions: {{ Js::from($questions) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Menara Kefahaman: Panjat Tangga Penguasaan'])

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- The 8-floor Tower Visualizer -->
        <div class="md:col-span-1 academic-card p-4 bg-white border border-slate-200 flex flex-col-reverse gap-1.5 h-96">
            <template x-for="f in 8" :key="f">
                <div 
                    class="flex-1 rounded-lg border text-xs font-bold font-mono flex items-center justify-between px-3 transition-all"
                    :class="f === currentFloor ? 'bg-indigo-900 border-indigo-900 text-white shadow-xs' : (f < currentFloor ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-50 border-slate-200 text-slate-400')"
                >
                    <span x-text="'Tingkat ' + f"></span>
                    <span x-show="f === currentFloor" class="text-xs">📍</span>
                    <span x-show="f === 8" class="text-xs">🏆</span>
                </div>
            </template>
        </div>

        <!-- Question Area -->
        <div class="md:col-span-3">
            <template x-if="currentQ && !completed">
                <div class="academic-card p-6 sm:p-8 bg-white border border-slate-200 space-y-6">
                    <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-100 pb-3">
                        <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded font-mono">Aras Kefahaman <span x-text="currentFloor"></span> / 8</span>
                        <span>Sasaran: Capai Puncak Tingkat 8</span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900" x-text="currentQ.question"></h3>

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
                            <div class="font-bold text-sm" x-text="feedbackData?.is_correct ? '✓ Tepat! Anda naik 1 tingkat.' : '✗ Kurang tepat. Anda turun 1 tingkat.'"></div>
                            <p x-text="feedbackData?.explanation"></p>
                            <div class="verbatim-quote bg-white/70">
                                "<span x-text="feedbackData?.quote"></span>"
                            </div>
                            <div class="pt-2 flex justify-end">
                                <button type="button" @click="nextStep()" class="px-5 py-2 rounded-lg bg-indigo-900 text-white font-semibold text-xs hover:bg-indigo-800 transition">
                                    Teruskan Panjat &rarr;
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    @include('activities.partials.activity-complete')
</div>

<script>
function towerActivity(config) {
    return {
        questions: config.questions || [],
        currentIndex: 0,
        currentQ: null,
        currentFloor: 1,
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

            window.addEventListener('keydown', (e) => {
                if (this.answered || this.completed) return;
                if (['1', '2', '3', '4'].includes(e.key)) {
                    this.submitAnswer(parseInt(e.key) - 1);
                }
            });
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
                    this.currentFloor = Math.min(8, this.currentFloor + 1);
                } else {
                    window.AcademicAudio.incorrect();
                    this.wrongCount++;
                    this.combo = 0;
                    this.currentFloor = Math.max(1, this.currentFloor - 1);
                }
            } catch (e) {
                this.answered = false;
            }
        },

        nextStep() {
            if (this.currentFloor >= 8) {
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
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'tower',
                    topic_id: config.topicId,
                    score: this.score,
                    right_count: this.rightCount,
                    wrong_count: this.wrongCount,
                    best_combo: this.bestCombo,
                    duration_seconds: 50
                });
                this.nextMission = res.next_mission;
            } catch (e) {}
        }
    };
}
</script>
@endsection
