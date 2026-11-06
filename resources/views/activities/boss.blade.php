@extends('layouts.app', ['title' => 'Penilaian Penguasaan: ' . ($topic ? $topic->name : 'Umum'), 'deck' => $deck])

@section('content')
<div 
    class="max-w-3xl mx-auto py-2"
    x-data="bossActivity({
        verifyUrl: '{{ route('decks.verify-answer', $deck) }}',
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        lossUrl: '{{ route('decks.boss-loss', $deck) }}',
        deckId: {{ $deck->id }},
        topicId: {{ $topic ? $topic->id : ($questions->first()?->topic_id ?? 0) }},
        topicName: '{{ addslashes($topic ? $topic->name : ($questions->first()?->topic?->name ?? 'Topik')) }}',
        initialHp: {{ $topic ? $topic->hp : 3 }},
        maxHp: {{ $topic ? $topic->max_hp : 3 }},
        hintActive: {{ ($topic && ($topic->strategy['hint'] ?? false)) ? 'true' : 'false' }},
        questions: {{ Js::from($questions) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Penilaian Penguasaan: ' . ($topic ? $topic->name : 'Topik Utama')])

    <!-- Boss Status Bar -->
    <div class="academic-card p-5 bg-white border border-slate-200 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xl shadow-xs">
                <svg class="w-6 h-6 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"></polyline>
                    <line x1="13" y1="19" x2="19" y2="13"></line>
                    <line x1="16" y1="16" x2="20" y2="20"></line>
                    <polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"></polyline>
                    <line x1="5" y1="14" x2="9" y2="18"></line>
                    <line x1="7" y1="17" x2="4" y2="20"></line>
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase text-slate-400">Sasaran Penguasaan</span>
                <h3 class="text-base font-bold text-slate-900" x-text="topicName"></h3>
            </div>
        </div>

        <div class="flex items-center space-x-6">
            <!-- Concept HP -->
            <div class="space-y-1">
                <div class="flex justify-between text-xs text-slate-500 font-medium">
                    <span>Ketahanan Topik:</span>
                    <span class="font-bold text-slate-800" x-text="hp + '/' + maxHp + ' HP'"></span>
                </div>
                <div class="w-36 bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + ((hp / maxHp) * 100) + '%'"></div>
                </div>
            </div>

            <!-- Student Lives -->
            <div class="text-right">
                <span class="text-[10px] uppercase font-semibold text-slate-400 block">Peluang</span>
                <div class="text-rose-600 font-bold tracking-wider text-sm font-mono" x-text="'♥ '.repeat(hearts) + '♡ '.repeat(Math.max(0, 3 - hearts))"></div>
            </div>
        </div>
    </div>

    <!-- Active Question Card -->
    <template x-if="currentQ && !completed && !failed">
        <div class="academic-card p-6 sm:p-8 bg-white border border-slate-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-100 pb-3">
                <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded font-mono">Soalan <span x-text="currentIndex + 1"></span> / <span x-text="questions.length"></span></span>
                <span class="text-slate-400">Pilih jawapan yang paling tepat berdasarkan nota</span>
            </div>

            <!-- Scaffolding Hint -->
            <template x-if="hintActive && currentQ.quote">
                <div class="verbatim-quote">
                    <strong class="font-sans font-semibold not-italic text-indigo-900 text-xs block mb-0.5">Petunjuk Ejen:</strong>
                    "<span x-text="currentQ.quote"></span>"
                </div>
            </template>

            <!-- Question Stem -->
            <h3 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug" x-text="currentQ.question"></h3>

            <!-- Options (1-4) -->
            <div class="grid grid-cols-1 gap-3">
                <template x-for="(opt, idx) in currentQ.options" :key="idx">
                    <button 
                        type="button" 
                        @click="submitAnswer(idx)"
                        :disabled="answered"
                        class="p-4 rounded-xl border text-left text-sm font-medium transition flex items-center justify-between"
                        :class="getOptionClass(idx)"
                    >
                        <div class="flex items-center space-x-3">
                            <span 
                                class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold font-mono"
                                :class="answered && idx === feedbackData?.correct_index ? 'bg-emerald-600 text-white' : (answered && idx === selectedIdx ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-700')"
                                x-text="['A', 'B', 'C', 'D'][idx]"
                            ></span>
                            <span class="text-slate-900 font-normal leading-relaxed" x-text="opt"></span>
                        </div>
                        <span class="text-xs font-mono text-slate-400 hidden sm:inline" x-text="'[' + (idx + 1) + ']'"></span>
                    </button>
                </template>
            </div>

            <!-- Verbatim Feedback Panel -->
            <template x-if="answered">
                <div 
                    class="p-4 rounded-xl border text-xs space-y-1.5 animate-fadeIn"
                    :class="feedbackData?.is_correct ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : 'bg-rose-50/80 border-rose-200 text-rose-900'"
                >
                    <div class="font-bold flex items-center space-x-1.5 text-sm">
                        <span x-text="feedbackData?.is_correct ? '✓ Tepat Sekali' : '✗ Kurang Tepat'"></span>
                    </div>
                    <p class="leading-relaxed font-sans" x-text="feedbackData?.explanation"></p>
                    <div class="verbatim-quote bg-white/70">
                        <strong class="font-sans font-semibold not-italic block mb-0.5">Petikan Verbatim Nota Sumber:</strong>
                        "<span x-text="feedbackData?.quote"></span>"
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button 
                            type="button" 
                            @click="nextQuestion()"
                            class="px-5 py-2 rounded-lg bg-indigo-900 hover:bg-indigo-800 text-white font-semibold text-xs transition shadow-xs"
                        >
                            Teruskan &rarr;
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- Loss State (Pelajar tewas kepada boss) -->
    <template x-if="failed">
        <div class="academic-card p-8 bg-white border border-slate-200 text-center space-y-4">
            <div class="w-12 h-12 mx-auto rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                ✕
            </div>
            <h3 class="text-xl font-bold text-slate-900">Cabaran Belum Berjaya Kali Ini</h3>
            <p class="text-xs text-slate-600 max-w-md mx-auto leading-relaxed">
                Ejen pedagogi telah merendahkan aras soalan dan menyediakan petunjuk rujukan untuk percubaan seterusnya. Anda disyorkan menyemak flashcard terlebih dahulu.
            </p>
            <div class="flex items-center justify-center gap-3 pt-2">
                <a href="{{ route('decks.activities.show', [$deck, 'cards', 'topic_id' => $topic ? $topic->id : null]) }}" class="px-5 py-2.5 rounded-xl bg-indigo-900 text-white text-xs font-semibold hover:bg-indigo-800 transition">
                    Ulang Kaji Flashcard
                </a>
                <button type="button" @click="location.reload()" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                    Cuba Semula
                </button>
            </div>
        </div>
    </template>

    @include('activities.partials.activity-complete')
</div>

<script>
function bossActivity(config) {
    return {
        questions: config.questions || [],
        currentIndex: 0,
        currentQ: null,
        hp: config.initialHp,
        maxHp: config.maxHp,
        topicName: config.topicName,
        hintActive: config.hintActive,
        hearts: 3,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        answered: false,
        selectedIdx: null,
        feedbackData: null,
        completed: false,
        failed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            if (this.questions.length > 0) {
                this.currentQ = this.questions[0];
            }
            window.addEventListener('keydown', (e) => {
                if (this.answered || this.completed || this.failed) return;
                if (['1', '2', '3', '4'].includes(e.key)) {
                    this.submitAnswer(parseInt(e.key) - 1);
                }
            });
        },

        getOptionClass(idx) {
            if (!this.answered) {
                return 'border-slate-200 bg-white hover:border-indigo-400 hover:bg-indigo-50/20';
            }
            if (idx === this.feedbackData?.correct_index) {
                return 'border-emerald-500 bg-emerald-50/70 text-emerald-950 font-semibold';
            }
            if (idx === this.selectedIdx) {
                return 'border-rose-400 bg-rose-50/70 text-rose-950';
            }
            return 'border-slate-200 bg-slate-50/50 opacity-50';
        },

        async submitAnswer(idx) {
            if (this.answered || !this.currentQ) return;
            this.answered = true;
            this.selectedIdx = idx;

            try {
                const res = await window.secureFetch(config.verifyUrl, {
                    question_id: this.currentQ.id,
                    selected_index: idx,
                    is_boss: true
                });

                this.feedbackData = res;

                if (res.is_correct) {
                    window.AcademicAudio.correct();
                    this.rightCount++;
                    this.combo++;
                    this.bestCombo = Math.max(this.bestCombo, this.combo);
                    const pts = 15 + Math.min(this.combo * 2, 20);
                    this.score += pts;
                    this.hp = Math.max(0, this.hp - 1);
                } else {
                    window.AcademicAudio.incorrect();
                    this.wrongCount++;
                    this.combo = 0;
                    this.hearts--;
                }
            } catch (err) {
                alert('Gagal mengesahkan jawapan: ' + err.message);
                this.answered = false;
            }
        },

        async nextQuestion() {
            if (this.hp <= 0) {
                await this.finishSession();
                return;
            }
            if (this.hearts <= 0) {
                this.failed = true;
                await window.secureFetch(config.lossUrl, { topic_id: config.topicId }).catch(() => {});
                return;
            }

            this.currentIndex++;
            if (this.currentIndex >= this.questions.length) {
                await this.finishSession();
                return;
            }

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
                    activity_type: 'boss',
                    topic_id: config.topicId,
                    score: this.score,
                    right_count: this.rightCount,
                    wrong_count: this.wrongCount,
                    best_combo: this.bestCombo,
                    duration_seconds: 45
                });
                this.nextMission = res.next_mission;
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
