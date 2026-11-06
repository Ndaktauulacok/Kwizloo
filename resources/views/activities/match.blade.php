@extends('layouts.app', ['title' => 'Padanan Konsep (Padan Pantas)', 'deck' => $deck])

@section('content')
<div 
    class="max-w-4xl mx-auto py-2"
    x-data="matchActivity({
        finishUrl: '{{ route('decks.finish-activity', $deck) }}',
        topicId: {{ $topic ? $topic->id : 'null' }},
        items: {{ Js::from($flashcards->take(6)->map(fn($f) => ['id' => $f->id, 'front' => $f->front, 'back' => $f->back, 'quote' => $f->quote])) }}
    })"
>
    @include('activities.partials.activity-header', ['title' => 'Padanan Konsep: Hubungkan Istilah & Definisi'])

    <div class="academic-card p-6 bg-white border border-slate-200 space-y-4 mb-6">
        <div class="flex items-center justify-between text-xs text-slate-500 border-b border-slate-100 pb-3">
            <span>Pilih satu istilah di sebelah kiri, kemudian pilih definisinya di sebelah kanan.</span>
            <span class="font-mono font-bold text-slate-800">Padanan: <span x-text="matchedCount + ' / ' + items.length"></span></span>
        </div>

        <!-- Matching Columns Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left: Terms -->
            <div class="space-y-2.5">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Istilah / Konsep</span>
                <template x-for="item in leftList" :key="'l-' + item.id">
                    <button 
                        type="button"
                        @click="selectLeft(item)"
                        :disabled="item.matched"
                        class="w-full p-4 rounded-xl border text-left text-sm font-semibold transition cursor-pointer"
                        :class="getLeftClass(item)"
                    >
                        <span x-text="item.front"></span>
                    </button>
                </template>
            </div>

            <!-- Right: Definitions -->
            <div class="space-y-2.5">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Definisi / Penerangan</span>
                <template x-for="item in rightList" :key="'r-' + item.id">
                    <button 
                        type="button"
                        @click="selectRight(item)"
                        :disabled="item.matched"
                        class="w-full p-4 rounded-xl border text-left text-sm leading-relaxed transition cursor-pointer"
                        :class="getRightClass(item)"
                    >
                        <span x-text="item.back"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    @include('activities.partials.activity-complete')
</div>

<script>
function matchActivity(config) {
    return {
        items: config.items || [],
        leftList: [],
        rightList: [],
        selectedLeft: null,
        selectedRight: null,
        matchedCount: 0,
        score: 0,
        combo: 0,
        bestCombo: 0,
        rightCount: 0,
        wrongCount: 0,
        completed: false,
        accuracy: 0,
        nextMission: null,

        init() {
            this.leftList = [...this.items].map(i => ({ ...i, matched: false })).sort(() => Math.random() - 0.5);
            this.rightList = [...this.items].map(i => ({ ...i, matched: false })).sort(() => Math.random() - 0.5);
        },

        getLeftClass(item) {
            if (item.matched) return 'border-emerald-200 bg-emerald-50 text-emerald-800 opacity-30 cursor-not-allowed';
            if (this.selectedLeft?.id === item.id) return 'border-indigo-600 bg-indigo-50 text-indigo-950 ring-2 ring-indigo-200';
            return 'border-slate-200 bg-white hover:border-indigo-400 hover:bg-slate-50 text-slate-900';
        },

        getRightClass(item) {
            if (item.matched) return 'border-emerald-200 bg-emerald-50 text-emerald-800 opacity-30 cursor-not-allowed';
            if (this.selectedRight?.id === item.id) return 'border-indigo-600 bg-indigo-50 text-indigo-950 ring-2 ring-indigo-200';
            return 'border-slate-200 bg-white hover:border-indigo-400 hover:bg-slate-50 text-slate-800';
        },

        selectLeft(item) {
            if (item.matched) return;
            this.selectedLeft = item;
            if (this.selectedRight) {
                this.checkPair();
            }
        },

        selectRight(item) {
            if (item.matched) return;
            this.selectedRight = item;
            if (this.selectedLeft) {
                this.checkPair();
            }
        },

        checkPair() {
            const isMatch = this.selectedLeft.id === this.selectedRight.id;

            if (isMatch) {
                window.AcademicAudio.correct();
                this.selectedLeft.matched = true;
                this.selectedRight.matched = true;
                this.matchedCount++;
                this.rightCount++;
                this.combo++;
                this.bestCombo = Math.max(this.bestCombo, this.combo);
                this.score += 20 + (this.combo * 2);

                this.selectedLeft = null;
                this.selectedRight = null;

                if (this.matchedCount >= this.items.length) {
                    setTimeout(() => this.finishSession(), 500);
                }
            } else {
                window.AcademicAudio.incorrect();
                this.wrongCount++;
                this.combo = 0;
                setTimeout(() => {
                    this.selectedLeft = null;
                    this.selectedRight = null;
                }, 300);
            }
        },

        async finishSession() {
            this.completed = true;
            window.AcademicAudio.complete();
            const total = this.rightCount + this.wrongCount;
            this.accuracy = total > 0 ? Math.round((this.rightCount / total) * 100) : 0;

            try {
                const res = await window.secureFetch(config.finishUrl, {
                    activity_type: 'match',
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
