@extends('layouts.app', ['title' => 'Pengesahan Kritis (Betul atau Salah)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-2xl mx-auto py-2"
    x-data="tfActivity({
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        questions: {{ Js::from($questions) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Pengesahan Kritis: Betul atau Salah'])

    <div class="academic-card p-4 bg-white border border-slate-200 mb-6 flex items-center justify-between">
        <span class="text-xs text-slate-500 font-semibold">Pernyataan <span class="font-mono text-slate-800" x-text="currentIndex + 1 + ' / ' + statements.length"></span></span>
        <span class="text-xs text-slate-400">Tekan [1] untuk Salah, [2] untuk Betul</span>
    </div>

    <!-- Active Statement Card -->
    <template x-if="currentStatement && !completed">
        <div class="academic-card p-6 sm:p-8 bg-white border border-slate-200 space-y-6 text-center">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                Analisis Pernyataan Berikut:
            </span>

            <div class="space-y-3">
                <p class="text-xs text-slate-400 font-medium" x-text="currentStatement.question"></p>
                <h3 class="text-xl sm:text-2xl font-serif italic text-slate-900 leading-snug px-4 py-2" x-text="'“' + currentStatement.shownText + '”'"></h3>
            </div>

            <!-- Decision Buttons -->
            <div class="grid grid-cols-2 gap-4 pt-4">
                <button 
                    type="button" 
                    @click="answer(false)"
                    :disabled="answered"
                    class="py-4 px-6 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-base transition flex flex-col items-center justify-center space-y-1 shadow-2xs"
                >
                    <span>Salah</span>
                    <span class="text-[11px] font-mono text-rose-500 font-normal">[1]</span>
                </button>

                <button 
                    type="button" 
                    @click="answer(true)"
                    :disabled="answered"
                    class="py-4 px-6 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-base transition flex flex-col items-center justify-center space-y-1 shadow-2xs"
                >
                    <span>Betul</span>
                    <span class="text-[11px] font-mono text-emerald-500 font-normal">[2]</span>
                </button>
            </div>

            <!-- Feedback -->
            <template x-if="answered">
                <div 
                    class="p-4 rounded-xl border text-xs text-left space-y-1"
                    :class="lastCorrect ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'"
                >
                    <div class="font-bold text-sm" x-text="lastCorrect ? '✓ Tepat' : '✗ Kurang Tepat'"></div>
                    <p x-text="currentStatement.explanation"></p>
                    <div class="verbatim-quote bg-white/70">
                        "<span x-text="currentStatement.quote"></span>"
                    </div>
                </div>
            </template>
        </div>
    </template>

    @include('activities.partials.activity-complete')
</div>

<script>
function tfActivity(config) {
    return {
        questions: config.questions || [],
        statements: [],
        currentIndex: 0,
        currentStatement: null,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        answered: false,
        lastCorrect: false,
        completed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            // Transform questions into TF statements (50% true, 50% false distractor)
            this.statements = this.questions.map(q => {
                const isTrue = Math.random() < 0.5;
                const correctOption = q.options[q.correct_index];
                const wrongOptions = q.options.filter((_, i) => i !== q.correct_index);
                const randomWrong = wrongOptions[Math.floor(Math.random() * wrongOptions.length)] || 'Pilihan salah';

                return {
                    question: q.question,
                    shownText: isTrue ? correctOption : randomWrong,
                    isTrue: isTrue,
                    correctOption: correctOption,
                    explanation: q.explanation,
                    quote: q.quote
                };
            }).slice(0, 10);

            if (this.statements.length > 0) {
                this.currentStatement = this.statements[0];
            }

            window.addEventListener('keydown', (e) => {
                if (this.answered || this.completed) return;
                if (e.key === '1') this.answer(false);
                if (e.key === '2') this.answer(true);
            });
        },

        answer(selectedBool) {
            if (this.answered || !this.currentStatement) return;
            this.answered = true;
            this.lastCorrect = selectedBool === this.currentStatement.isTrue;

            if (this.lastCorrect) {
                window.AcademicAudio.correct();
                this.rightCount++;
                this.combo++;
                this.bestCombo = Math.max(this.bestCombo, this.combo);
                this.score += 15 + (this.combo * 2);
            } else {
                window.AcademicAudio.incorrect();
                this.wrongCount++;
                this.combo = 0;
            }

            setTimeout(() => {
                this.nextStatement();
            }, 1200);
        },

        nextStatement() {
            this.currentIndex++;
            if (this.currentIndex >= this.statements.length) {
                this.finishSession();
                return;
            }
            this.currentStatement = this.statements[this.currentIndex];
            this.answered = false;
        },

        async finishSession() {
            this.completed = true;
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'tf',
                    topic_id: config.topicId,
                    score: this.score,
                    right_count: this.rightCount,
                    wrong_count: this.wrongCount,
                    best_combo: this.bestCombo,
                    duration_seconds: 35
                });
                this.nextMission = res.next_mission;
            } catch (e) {}
        }
    };
}
</script>
@endsection
