@extends('layouts.app', ['title' => 'Selamat Datang'])

@section('content')
<div class="max-w-6xl mx-auto space-y-16 sm:space-y-24">
    <!-- Hero -->
    <section class="grid grid-cols-1 lg:grid-cols-[0.95fr_1.15fr] gap-10 lg:gap-6 items-center pt-2 pb-10">
        <div class="order-2 lg:order-1 min-w-0 text-center lg:text-left">
            <span class="sticker bg-white text-ink -rotate-2">
                <span class="w-2 h-2 rounded-full bg-tomato animate-pulse"></span>
                Bersama Syed, Mentor AI Anda
            </span>

            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold text-ink leading-[1.05]">
                Belajar lebih bijak dengan <span class="marker-underline">bantuan AI</span>.
            </h1>

            <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                Muat naik nota atau dokumen PDF anda. AI KwizLoo akan membaca kandungannya, mengenal pasti topik penting
                dan menjana kuiz, kad imbas serta aktiviti interaktif untuk anda. Sesuai untuk murid, pelajar dan guru.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5">
                <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white text-base sm:text-lg px-7 py-3.5 rounded-2xl group w-full sm:w-auto">
                    <span>Mulakan Sekarang</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
                <button
                    type="button"
                    @click="$dispatch('open-onboarding')"
                    class="btn-pop bg-sun text-ink text-sm sm:text-base px-5 py-3.5 rounded-2xl flex items-center justify-center gap-2 group w-full sm:w-auto"
                >
                    <svg class="w-5 h-5 text-ink fill-ink/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                    <span>Panduan Pantas</span>
                </button>
            </div>

            <div class="mt-4 text-center lg:text-left">
                <a href="{{ route('how-it-works') }}" class="text-xs font-bold text-slate-600 hover:text-ink underline decoration-2 decoration-slate-300 hover:decoration-ink underline-offset-4">
                    Ketahui lebih lanjut mengenai cara AI berfungsi &rarr;
                </a>
            </div>

            <dl class="mt-10 grid grid-cols-3 gap-3 max-w-md mx-auto lg:mx-0">
                <div class="p-3 rounded-xl bg-white border-2 border-ink text-center shadow-pop-sm">
                    <dt class="sr-only">Mod pembelajaran</dt>
                    <dd class="font-display text-2xl font-extrabold text-ink">9</dd>
                    <dd class="text-[11px] font-semibold text-slate-600">mod pembelajaran</dd>
                </div>
                <div class="p-3 rounded-xl bg-white border-2 border-ink text-center shadow-pop-sm">
                    <dt class="sr-only">Format disokong</dt>
                    <dd class="font-display text-2xl font-extrabold text-ink">PDF</dd>
                    <dd class="text-[11px] font-semibold text-slate-600">atau teks biasa</dd>
                </div>
                <div class="p-3 rounded-xl bg-white border-2 border-ink text-center shadow-pop-sm">
                    <dt class="sr-only">Dijana oleh AI</dt>
                    <dd class="font-display text-2xl font-extrabold text-ink">AI</dd>
                    <dd class="text-[11px] font-semibold text-slate-600">jana soalan automatik</dd>
                </div>
            </dl>
        </div>

        <!-- Interactive Furry Mascot (Syed Si Serigala Sarjana) -->
        <div class="order-1 lg:order-2 min-w-0 w-full max-w-[560px] mx-auto select-none" x-data="mascotHero()">
            <div
                x-ref="stage"
                @mousemove="handleMouseMove($event)"
                @mouseleave="handleMouseLeave()"
                class="relative flex flex-col items-center justify-center pt-1 pb-2"
            >
                <!-- Interactive Mascot Speech Bubble -->
                <div class="w-full max-w-md mb-2.5 relative z-30 transition-transform duration-200">
                    <button
                        type="button"
                        @click="petMascot()"
                        class="w-full academic-card p-3 bg-white border-2 border-ink shadow-pop-sm flex items-center gap-2.5 text-left group hover:bg-amber-50/70 transition relative"
                    >
                        <!-- Speech bubble notch pointing down -->
                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-3.5 h-3.5 bg-white border-r-2 border-b-2 border-ink rotate-45 group-hover:bg-amber-50/70 transition"></div>

                        <span class="shrink-0">@include('partials.syed-avatar', ['class' => 'w-8 h-8'])</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="font-display font-extrabold text-[11px] text-indigo-600 tracking-wide uppercase">Prof. Syed · Mentor AI</span>
                                <span class="text-[10px] text-slate-400 group-hover:text-ink font-semibold flex items-center gap-0.5">
                                    <span>Petua seterusnya</span>
                                    <span class="text-xs">&rarr;</span>
                                </span>
                            </div>
                            <p class="text-xs font-semibold text-slate-700 leading-snug truncate" x-text="dialogue"></p>
                        </div>
                    </button>
                </div>

                <!-- Syed, the scholar wolf -->
                <div
                    @click="petMascot()"
                    @keydown.enter.prevent="petMascot()"
                    @keydown.space.prevent="petMascot()"
                    role="button"
                    tabindex="0"
                    aria-label="Sapa Syed"
                    class="relative w-full max-w-[460px] mx-auto cursor-pointer rounded-3xl transition-transform duration-300 ease-out will-change-transform"
                    :style="'transform: perspective(900px) rotateX(' + tiltX + 'deg) rotateY(' + tiltY + 'deg);'"
                >
                    @include('partials.syed-scene')

                    <!-- XP pops on click -->
                    <template x-for="s in sparkles" :key="s.id">
                        <span
                            class="absolute pointer-events-none font-display font-extrabold text-xs sm:text-sm text-ink bg-sun px-2 py-0.5 rounded-full border-2 border-ink shadow-pop-sm animate-spark-fly z-40 whitespace-nowrap"
                            :style="'left: ' + s.x + '%; top: ' + s.y + '%;'"
                         x-text="s.label"></span>
                    </template>

                    <!-- Name plate -->
                    <div class="absolute bottom-[3%] left-1/2 -translate-x-1/2 whitespace-nowrap bg-paper text-ink border-2 border-ink px-3 py-1 rounded-full text-xs font-extrabold shadow-pop-sm flex items-center gap-1.5 z-20">
                        <span class="w-2 h-2 rounded-full bg-mint animate-pulse"></span>
                        <span>Prof. Syed</span>
                        <span class="bg-indigo-600 text-paper text-[10px] px-1.5 py-0.5 rounded-md font-mono">Lv.<span x-text="level"></span></span>
                    </div>
                </div>

                <!-- Interactive Companion Cards (Speed Quiz & 3D Flashcard) -->
                <div class="w-full mt-4 hidden sm:grid grid-cols-2 gap-3 z-20">
                    <!-- Card 1: Playable Speed Quiz Card -->
                    <div class="academic-card p-3 bg-indigo-50 border-2 border-ink shadow-pop-sm transition-transform hover:-translate-y-0.5 duration-150 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="px-2 py-0.5 rounded-md bg-sky-pop border border-ink text-[10px] font-black uppercase text-ink tracking-wider">
                                    Kuiz Pantas
                                </span>
                                <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    
                                    <span>Streak: <span x-text="streak" class="font-extrabold text-indigo-600"></span></span>
                                </span>
                            </div>

                            <!-- Question Prompt -->
                            <div class="mt-1.5 min-h-[38px]">
                                <p class="text-[11px] font-bold text-ink leading-tight" x-text="questions[quizIndex].q"></p>
                            </div>
                        </div>

                        <!-- Choice Buttons -->
                        <div x-show="!quizAnswered" class="mt-2 grid grid-cols-2 gap-1.5">
                            <button
                                type="button"
                                @click="answerQuiz(false)"
                                class="px-2 py-1.5 rounded-lg bg-white hover:bg-slate-100 border-2 border-ink text-[11px] font-bold text-ink shadow-pop-sm active:translate-y-0.5 transition"
                            >
                                Salah
                            </button>
                            <button
                                type="button"
                                @click="answerQuiz(true)"
                                class="px-2 py-1.5 rounded-lg bg-ink hover:bg-slate-800 border-2 border-ink text-[11px] font-bold text-white shadow-pop-sm active:translate-y-0.5 transition"
                            >
                                Betul
                            </button>
                        </div>

                        <!-- Result Feedback -->
                        <div x-show="quizAnswered" class="mt-2 pt-2 border-t border-ink/20 space-y-1" style="display: none;">
                            <div class="flex items-center justify-between text-[11px] font-extrabold">
                                <span :class="isCorrect ? 'text-emerald-700' : 'text-tomato'" x-text="isCorrect ? '✓ Tepat! (+25 XP)' : '✕ Kurang Tepat'"></span>
                                <button
                                    type="button"
                                    @click="nextQuiz()"
                                    class="text-[10px] text-indigo-600 hover:text-ink font-bold underline underline-offset-2"
                                >
                                    Lain &rarr;
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-600 leading-tight" x-text="quizFeedback"></p>
                        </div>
                    </div>

                    <!-- Card 2: 3D Flippable Flashcard -->
                    <div
                        class="cursor-pointer card-flip-container min-h-[125px]"
                        @click="toggleFlip()"
                    >
                        <div
                            class="card-flip-inner relative w-full h-full min-h-[125px]"
                            :class="{ 'is-flipped': isFlipped }"
                        >
                            <!-- Front Face -->
                            <div class="card-face absolute inset-0 academic-card p-3 bg-sun/20 border-2 border-ink shadow-pop-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md bg-sun border border-ink text-[10px] font-black uppercase text-ink tracking-wider">
                                            Kad Imbas
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-500">Balikkan ↺</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-[9px] uppercase font-bold text-slate-400">Istilah:</span>
                                        <h4 class="font-extrabold text-sm text-ink leading-snug" x-text="flashcards[flashcardIndex].term"></h4>
                                    </div>
                                </div>
                                <div class="text-[10px] font-semibold text-indigo-600 flex items-center justify-between mt-1">
                                    <span>Klik untuk maksud</span>
                                    <span class="text-xs">&rarr;</span>
                                </div>
                            </div>

                            <!-- Back Face -->
                            <div class="card-face card-face-back absolute inset-0 academic-card p-3 bg-white border-2 border-ink shadow-pop-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded-md bg-mint/30 border border-ink text-[9px] font-black uppercase text-ink" x-text="flashcards[flashcardIndex].category"></span>
                                        <button
                                            type="button"
                                            @click.stop="nextFlashcard()"
                                            class="text-[10px] font-bold text-indigo-600 hover:text-ink underline"
                                        >
                                            Lain ↻
                                        </button>
                                    </div>
                                    <p class="mt-1 text-[10px] text-slate-700 leading-snug" x-text="flashcards[flashcardIndex].def"></p>
                                </div>
                                <div class="text-[10px] font-bold text-emerald-700 text-right mt-0.5">
                                    ✓ Faham (+10 XP)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- XP & Academic Rank Bar (Bottom) -->
                <div class="w-full mt-3 relative z-20">
                    <div class="p-2 rounded-xl bg-white/95 backdrop-blur border-2 border-ink shadow-pop-sm flex items-center justify-between gap-2.5 text-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="shrink-0">@include('partials.syed-avatar', ['class' => 'w-7 h-7'])</span>
                            <div class="min-w-0">
                                <div class="font-bold text-ink truncate text-[11px]">
                                    Tahap <span x-text="level"></span>: <span x-text="levelTitle" class="text-indigo-600"></span>
                                </div>
                                <div class="w-24 sm:w-32 bg-slate-100 h-1.5 rounded-full border border-ink overflow-hidden mt-0.5">
                                    <div class="bg-mint h-full transition-all duration-300 rounded-full" :style="'width: ' + Math.min(100, Math.round(xp / maxXp * 100)) + '%'"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-mono font-bold text-[10px] text-slate-700"><span x-text="xp"></span>/<span x-text="maxXp"></span> XP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INTERACTIVE STUDY LAB WITH PROF. SYED -->
    <section class="academic-card p-6 sm:p-8 bg-amber-50/50 border-2 border-ink relative" x-data="{ activeLab: 'boss', bossHp: 3, activeQuizChoice: null, quizSolved: false }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b-2 border-dashed border-slate-300">
            <div class="flex items-start sm:items-center gap-3">
                <div class="shrink-0">@include('partials.syed-avatar', ['class' => 'w-12 h-12'])</div>
                <div>
                    <span class="sticker bg-sun text-ink -rotate-1 text-[11px]">Makmal Interaktif Syed</span>
                    <h2 class="mt-1 text-2xl sm:text-3xl font-extrabold text-ink">
                        Cuba Simulasi Mod KwizLoo Sekarang
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                        Pilih mod di bawah untuk uji minda anda secara langsung sebelum memuat naik sebarang nota.
                    </p>
                </div>
            </div>

            <!-- Mode Selector Tabs -->
            <div class="flex items-center flex-wrap gap-2">
                <button
                    type="button"
                    @click="activeLab = 'boss'; window.AcademicAudio?.click()"
                    class="btn-pop text-xs sm:text-sm px-3.5 py-2 rounded-xl transition"
                    :class="activeLab === 'boss' ? 'bg-tomato text-white' : 'bg-white text-ink'"
                >
                    Cabaran Bos
                </button>
                <button
                    type="button"
                    @click="activeLab = 'speed'; window.AcademicAudio?.click()"
                    class="btn-pop text-xs sm:text-sm px-3.5 py-2 rounded-xl transition"
                    :class="activeLab === 'speed' ? 'bg-sun text-ink' : 'bg-white text-ink'"
                >
                    Ujian Pantas (60s)
                </button>
                <button
                    type="button"
                    @click="activeLab = 'cards'; window.AcademicAudio?.click()"
                    class="btn-pop text-xs sm:text-sm px-3.5 py-2 rounded-xl transition"
                    :class="activeLab === 'cards' ? 'bg-mint text-ink' : 'bg-white text-ink'"
                >
                    Kad Imbas
                </button>
            </div>
        </div>

        <!-- Tab 1: Cabaran Bos Preview -->
        <div x-show="activeLab === 'boss'" class="mt-6 space-y-4" x-transition>
            <div class="p-5 sm:p-6 rounded-2xl bg-ink text-white border-2 border-ink shadow-pop-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-11 h-11 rounded-xl bg-tomato border-2 border-paper/20 flex items-center justify-center font-display font-extrabold text-sm text-white shrink-0">B2</span>
                        <div>
                            <div class="font-extrabold text-sm sm:text-base text-sun">Bos Bab 2: Monster Reaksi Gelap</div>
                            <div class="text-xs text-slate-300">Syed mengesan kelemahan: Perbezaan fungsi Stroma vs Grana dalam kloroplas!</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <span class="text-xs font-bold text-tomato tracking-wider uppercase">HP BOS:</span>
                        <div class="flex gap-1.5">
                            <template x-for="i in 3" :key="i">
                                <span class="w-6 h-6 rounded-lg border border-white/30 flex items-center justify-center transition-transform duration-200" :class="i <= bossHp ? 'bg-tomato text-white scale-105' : 'bg-slate-800 text-slate-600 opacity-40'"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21s-7.5-4.6-9.6-9.3C.9 8.2 3.1 4.5 6.8 4.5c2.1 0 3.6 1.1 5.2 3 1.6-1.9 3.1-3 5.2-3 3.7 0 5.9 3.7 4.4 7.2C19.5 16.4 12 21 12 21z"/></svg></span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-900 border border-slate-700">
                    <p class="text-sm font-semibold text-paper leading-snug">
                        "Di manakah berlakunya fasa tindak balas tidak bersandarkan cahaya (fasa gelap) di dalam kloroplas?"
                    </p>
                    <div class="mt-3.5 grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                        <button
                            type="button"
                            @click="if (bossHp > 0) { bossHp = Math.max(0, bossHp - 1); window.AcademicAudio?.correct(); quizSolved = true; }"
                            class="p-3 rounded-xl text-left font-bold transition flex items-center justify-between"
                            :class="quizSolved ? 'bg-mint text-ink border-2 border-mint shadow-pop-sm' : 'bg-slate-800 text-paper hover:bg-slate-700 border border-slate-600'"
                        >
                            <span>A. Stroma</span>
                            <span x-show="quizSolved" class="text-[11px] font-black uppercase">✓ Serangan Tepat!</span>
                        </button>
                        <button
                            type="button"
                            @click="window.AcademicAudio?.incorrect()"
                            class="p-3 rounded-xl text-left font-bold bg-slate-800 text-paper hover:bg-slate-700 border border-slate-600 transition"
                        >
                            <span>B. Grana (Granum)</span>
                        </button>
                        <button
                            type="button"
                            @click="window.AcademicAudio?.incorrect()"
                            class="p-3 rounded-xl text-left font-bold bg-slate-800 text-paper hover:bg-slate-700 border border-slate-600 transition"
                        >
                            <span>C. Membran Luar</span>
                        </button>
                        <button
                            type="button"
                            @click="window.AcademicAudio?.incorrect()"
                            class="p-3 rounded-xl text-left font-bold bg-slate-800 text-paper hover:bg-slate-700 border border-slate-600 transition"
                        >
                            <span>D. Tilakoid</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-paper/70 pt-1">
                    <span x-show="bossHp === 0" class="text-sun font-bold">Tahniah, bos tewas! Syed mengesahkan anda kini menguasai topik ini.</span>
                    <span x-show="bossHp > 0">Petua Syed: Klik jawapan A untuk kurangkan HP Bos.</span>
                    <button
                        type="button"
                        @click="bossHp = 3; quizSolved = false; window.AcademicAudio?.click()"
                        class="text-xs text-sun hover:underline font-bold self-start sm:self-auto"
                    >
                        Set Semula Simulasi ↺
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 2: Ujian Pantas Preview -->
        <div x-show="activeLab === 'speed'" class="mt-6 space-y-4" style="display: none;" x-transition>
            <div class="p-5 sm:p-6 rounded-2xl bg-white border-2 border-ink shadow-pop-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-sun border-2 border-ink flex items-center justify-center font-bold text-base shadow-pop-sm">⏱️</span>
                        <div>
                            <div class="font-extrabold text-sm sm:text-base text-ink">Mod Ujian Pantas (60 Saat)</div>
                            <div class="text-xs text-slate-500">Masa bergerak pantas. Jawab seberapa banyak soalan sebelum masa tamat!</div>
                        </div>
                    </div>
                    <div class="px-3.5 py-1.5 rounded-xl bg-ink text-white font-mono font-bold text-sm border-2 border-ink shadow-pop-sm">
                        00:42
                    </div>
                </div>
                <div class="p-4 rounded-xl bg-amber-50/70 border-2 border-ink text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed">
                    "Molekul klorofil teruja dan melepaskan elektron bertenaga tinggi apabila menyerap foton tenaga cahaya."
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button
                        type="button"
                        @click="window.AcademicAudio?.incorrect()"
                        class="btn-pop bg-white text-ink text-xs sm:text-sm py-2.5 rounded-xl"
                    >
                        Salah
                    </button>
                    <button
                        type="button"
                        @click="window.AcademicAudio?.correct()"
                        class="btn-pop bg-indigo-600 text-white text-xs sm:text-sm py-2.5 rounded-xl"
                    >
                        Betul (✓ Tepat)
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab 3: Kad Imbas Preview -->
        <div x-show="activeLab === 'cards'" class="mt-6 space-y-4" style="display: none;" x-transition>
            <div class="p-5 sm:p-6 rounded-2xl bg-white border-2 border-ink shadow-pop-sm space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600">Simulasi Kad Imbas Berjarak (Spaced Repetition)</span>
                    <span class="text-xs font-extrabold text-indigo-600">Kad 1 daripada 12</span>
                </div>
                <div class="p-6 rounded-xl bg-paper border-2 border-ink text-center space-y-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-sun border border-ink text-[11px] font-bold">Biologi SPM Bab 2</span>
                    <div class="text-lg font-extrabold text-ink">Apakah maksud 'Fotofosforilasi'?</div>
                    <p class="text-xs text-slate-600 max-w-md mx-auto leading-relaxed">
                        Sintesis ATP daripada ADP dan fosfat tak organik dengan menggunakan tenaga cahaya yang diserap oleh klorofil.
                    </p>
                </div>
                <div class="flex items-center justify-center flex-wrap gap-2.5 pt-2">
                    <button type="button" @click="window.AcademicAudio?.incorrect()" class="btn-pop bg-slate-100 text-ink text-xs px-4 py-2 rounded-xl">
                        Sukar (Ulang Kerap)
                    </button>
                    <button type="button" @click="window.AcademicAudio?.pop()" class="btn-pop bg-sun text-ink text-xs px-4 py-2 rounded-xl">
                        Sederhana (Ulang Esok)
                    </button>
                    <button type="button" @click="window.AcademicAudio?.correct()" class="btn-pop bg-mint text-ink text-xs px-4 py-2 rounded-xl">
                        Mudah (Dikuasai ✓)
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works in three steps -->
    <section class="relative pt-6">
        <div class="text-center max-w-3xl mx-auto">
            <span class="sticker bg-sun text-ink -rotate-1">Aliran Kerja Mudah</span>
            <h2 class="mt-4 text-3xl sm:text-5xl font-extrabold text-ink leading-tight">
                Cara KwizLoo Berfungsi dalam <span class="marker-underline">3 Langkah</span>
            </h2>
            <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed">
                Tiada tetapan yang rumit. Sediakan nota anda, dan AI akan menguruskan selebihnya.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            <!-- Step 1 -->
            <div class="academic-card p-6 sm:p-7 flex flex-col justify-between relative group hover:-translate-y-1 transition duration-200">
                <div class="absolute -top-4 -left-3 w-11 h-11 rounded-2xl bg-sky-pop border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-lg text-ink">
                    01
                </div>

                <div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Langkah Pertama</span>
                        <span class="px-2 py-0.5 rounded-md bg-sky-pop/20 border border-ink text-[11px] font-bold text-ink">Format Bebas</span>
                    </div>

                    <h3 class="mt-4 text-xl sm:text-2xl font-extrabold text-ink leading-snug">
                        Muat Naik atau Tampal Nota
                    </h3>
                    <p class="mt-2.5 text-sm text-slate-600 leading-relaxed">
                        Tampal teks ringkas atau seret dokumen PDF sukatan pelajaran. KwizLoo membaca keseluruhan kandungan tanpa memerlukan anda memformatnya terlebih dahulu.
                    </p>

                    <!-- Preview graphic -->
                    <div class="mt-5 p-3.5 rounded-xl bg-paper border-2 border-ink text-xs font-mono text-slate-700 space-y-1.5">
                        <div class="flex items-center gap-2 font-sans font-bold text-ink text-[11px]">
                            <svg class="w-4 h-4 text-sky-pop" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Nota_Sains_Bab2.pdf
                        </div>
                        <div class="text-[11px] text-slate-500 font-serif italic truncate">
                            "Tumbuhan menghasilkan glukosa melalui fotosintesis..."
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t-2 border-dashed border-slate-200 flex items-center justify-between text-xs font-bold text-slate-700">
                    <span>Sedia dalam 5 saat</span>
                    <button type="button" @click="$dispatch('open-onboarding')" class="text-indigo-600 hover:text-ink underline decoration-2 decoration-sun underline-offset-4">
                        Lihat panduan &rarr;
                    </button>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="academic-card p-6 sm:p-7 flex flex-col justify-between relative group hover:-translate-y-1 transition duration-200 bg-amber-50/40">
                <div class="absolute -top-4 -left-3 w-11 h-11 rounded-2xl bg-sun border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-lg text-ink">
                    02
                </div>

                <div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Langkah Kedua</span>
                        <span class="px-2 py-0.5 rounded-md bg-sun/30 border border-ink text-[11px] font-bold text-ink">Berkuasa AI</span>
                    </div>

                    <h3 class="mt-4 text-xl sm:text-2xl font-extrabold text-ink leading-snug">
                        AI Ekstrak & Jana Bahan
                    </h3>
                    <p class="mt-2.5 text-sm text-slate-600 leading-relaxed">
                        AI membedah nota anda kepada topik-topik teras, menyediakan soalan aneka pilihan, soalan pantas, dan kad imbas berserta petikan rujukan teks asal.
                    </p>

                    <!-- Preview graphic -->
                    <div class="mt-5 p-3.5 rounded-xl bg-white border-2 border-ink text-xs space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold">
                            <span class="text-tomato">Soalan Aras Tinggi</span>
                            <span class="text-mint font-extrabold">Dijana</span>
                        </div>
                        <div class="text-[11px] text-slate-700 leading-snug">
                            "Apakah faktor utama yang mempengaruhi kadar fotosintesis?"
                        </div>
                        <div class="text-[10px] text-slate-400 border-l-2 border-sun pl-2 italic">
                            Disahkan dengan petikan sumber teks.
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t-2 border-dashed border-slate-200 flex items-center justify-between text-xs font-bold text-slate-700">
                    <span>9 mod serentak</span>
                    <span class="text-mint">✓ 100% Automatik</span>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="academic-card p-6 sm:p-7 flex flex-col justify-between relative group hover:-translate-y-1 transition duration-200">
                <div class="absolute -top-4 -left-3 w-11 h-11 rounded-2xl bg-mint border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-lg text-ink">
                    03
                </div>

                <div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Langkah Ketiga</span>
                        <span class="px-2 py-0.5 rounded-md bg-mint/30 border border-ink text-[11px] font-bold text-ink">Interaktif</span>
                    </div>

                    <h3 class="mt-4 text-xl sm:text-2xl font-extrabold text-ink leading-snug">
                        Main Kuiz & Takluk Bos
                    </h3>
                    <p class="mt-2.5 text-sm text-slate-600 leading-relaxed">
                        Pilih aktiviti kegemaran anda. Jika anda kerap salah menjawab, ejen AI membangkitkan 'Cabaran Bos' untuk membaiki kelemahan anda sehingga mahir.
                    </p>

                    <!-- Preview graphic -->
                    <div class="mt-5 p-3.5 rounded-xl bg-ink text-white text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-sun">Cabaran Bos Aktif</span>
                            <span class="text-tomato font-bold text-[10px]">HP: 3/3</span>
                        </div>
                        <div class="w-full bg-slate-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-tomato h-full w-full"></div>
                        </div>
                        <div class="text-[10px] text-paper/70">
                            Ejen AI: "Fokus pada topik 'Reaksi Gelap' untuk pengukuhan."
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t-2 border-dashed border-slate-200 flex items-center justify-between text-xs font-bold text-slate-700">
                    <span>Laporan Guru Tersedia</span>
                    <a href="{{ route('decks.create') }}" class="text-indigo-600 hover:text-ink underline decoration-2 decoration-sun underline-offset-4">
                        Cuba sekarang &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 9 MODES SHOWCASE -->
    <section class="academic-card p-6 sm:p-10 bg-white">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="sticker bg-bubblegum/40 text-ink -rotate-1">Pelbagai Cara Belajar</span>
                <h2 class="mt-3 text-2xl sm:text-4xl font-extrabold text-ink">
                    9 Mod Pembelajaran Interaktif
                </h2>
                <p class="mt-2 text-sm sm:text-base text-slate-600 max-w-xl">
                    Semua aktiviti ini dijana secara langsung daripada nota anda. Pilih gaya pembelajaran yang paling sesuai dengan mood anda hari ini.
                </p>
            </div>
            <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white text-sm px-5 py-2.5 shrink-0">
                Ubah Nota Anda Sekarang
            </a>
        </div>

        @php
            $studyModes = [
                ['name' => 'Penilaian Penguasaan', 'tag' => 'Cabaran Bos', 'desc' => 'Soalan aras tinggi untuk mengukuhkan topik lemah.', 'bg' => 'bg-tomato', 'text' => 'text-white'],
                ['name' => 'Ujian Pantas', 'tag' => '60 Saat', 'desc' => 'Jawab seberapa banyak soalan sebelum masa tamat.', 'bg' => 'bg-sun', 'text' => 'text-ink'],
                ['name' => 'Sintesis Menurun', 'tag' => 'Refleks', 'desc' => 'Pilih jawapan yang betul sebelum blok jawapan jatuh ke bawah.', 'bg' => 'bg-sky-pop', 'text' => 'text-ink'],
                ['name' => 'Padanan Konsep', 'tag' => 'Kefahaman', 'desc' => 'Padankan istilah penting dengan maksud atau definisinya.', 'bg' => 'bg-bubblegum', 'text' => 'text-ink'],
                ['name' => 'Menara Kefahaman', 'tag' => '8 Aras', 'desc' => 'Bina menara pengetahuan aras demi aras tanpa tergelincir.', 'bg' => 'bg-mint', 'text' => 'text-ink'],
                ['name' => 'Pengesahan Kritis', 'tag' => 'Betul / Salah', 'desc' => 'Tentukan kesahihan fakta berdasarkan nota rujukan.', 'bg' => 'bg-[#bfcf95]', 'text' => 'text-ink'],
                ['name' => 'Rekonstruksi Istilah', 'tag' => 'Susun Ejaan', 'desc' => 'Susun semula huruf berselerak untuk membentuk istilah penting.', 'bg' => 'bg-[#e5ad78]', 'text' => 'text-ink'],
                ['name' => 'Flashcard Berjarak', 'tag' => 'Ingatan Aktif', 'desc' => 'Semak kad imbas pintar untuk ingatan jangka panjang.', 'bg' => 'bg-[#bcaed6]', 'text' => 'text-ink'],
                ['name' => 'Penanda Aras Adaptif', 'tag' => 'Lumba Bot', 'desc' => 'Bersaing dengan bot simulasi mengikut tahap kelajuan anda.', 'bg' => 'bg-[#93c3c4]', 'text' => 'text-ink'],
            ];
        @endphp

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
            @foreach($studyModes as $m)
                <div class="p-4 rounded-xl border-2 border-ink bg-paper/50 hover:bg-white transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded-md border border-ink text-[10px] font-black uppercase tracking-wider {{ $m['bg'] }} {{ $m['text'] }}">
                                {{ $m['tag'] }}
                            </span>
                        </div>
                        <h3 class="mt-2.5 font-extrabold text-base text-ink">{{ $m['name'] }}</h3>
                        <p class="mt-1 text-xs text-slate-600 leading-relaxed">{{ $m['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- FAQ -->
    <section class="max-w-4xl mx-auto" x-data="{ openFaq: null }">
        <div class="text-center">
            <span class="sticker bg-white text-ink -rotate-1">Soalan Lazim</span>
            <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-ink">
                Segala Yang Anda Perlu Tahu
            </h2>
            <p class="mt-2 text-slate-600 text-sm sm:text-base">
                Jawapan pantas untuk soalan yang sering ditanya oleh murid dan guru.
            </p>
        </div>

        <div class="mt-8 space-y-3">
            <!-- FAQ 1 -->
            <div class="academic-card overflow-hidden">
                <button
                    type="button"
                    @click="openFaq = openFaq === 1 ? null : 1"
                    class="w-full p-5 text-left font-extrabold text-base sm:text-lg text-ink flex items-center justify-between gap-4 hover:bg-slate-50 transition"
                    :aria-expanded="openFaq === 1"
                >
                    <span>Bagaimanakah KwizLoo menukar nota saya kepada kuiz?</span>
                    <span class="w-7 h-7 rounded-lg bg-sun border-2 border-ink flex items-center justify-center shrink-0 font-bold transition-transform duration-200" :class="openFaq === 1 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-sm sm:text-base text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    AI KwizLoo membaca dokumen atau teks nota anda, mengesan konsep-konsep penting, dan menstrukturkannya menjadi soalan berbilang pilihan, soalan betul/salah, kad imbas, serta aktiviti interaktif. Setiap soalan disertakan petikan rujukan daripada nota asal anda.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="academic-card overflow-hidden">
                <button
                    type="button"
                    @click="openFaq = openFaq === 2 ? null : 2"
                    class="w-full p-5 text-left font-extrabold text-base sm:text-lg text-ink flex items-center justify-between gap-4 hover:bg-slate-50 transition"
                    :aria-expanded="openFaq === 2"
                >
                    <span>Bolehkah saya mencuba tanpa memuat naik nota saya sendiri?</span>
                    <span class="w-7 h-7 rounded-lg bg-sun border-2 border-ink flex items-center justify-center shrink-0 font-bold transition-transform duration-200" :class="openFaq === 2 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-sm sm:text-base text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Ya! Kami menyediakan pek contoh segera (Biologi: Fotosintesis). Anda boleh mencuba kesemua 9 mod pembelajaran dalam 1 klik tanpa perlu menulis teks atau memuat naik sebarang fail.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="academic-card overflow-hidden">
                <button
                    type="button"
                    @click="openFaq = openFaq === 3 ? null : 3"
                    class="w-full p-5 text-left font-extrabold text-base sm:text-lg text-ink flex items-center justify-between gap-4 hover:bg-slate-50 transition"
                    :aria-expanded="openFaq === 3"
                >
                    <span>Apakah itu 'Cabaran Bos' dan bagaimana ia membantu saya?</span>
                    <span class="w-7 h-7 rounded-lg bg-sun border-2 border-ink flex items-center justify-center shrink-0 font-bold transition-transform duration-200" :class="openFaq === 3 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-sm sm:text-base text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Apabila anda melakukan kesilapan pada soalan sesuatu topik, ejen AI akan menandakan topik tersebut sebagai 'Bos'. Mod ini memberi tumpuan kepada soalan aras tinggi dan penjelasan khusus sehingga anda berjaya menguasai kelemahan tersebut.
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="academic-card overflow-hidden">
                <button
                    type="button"
                    @click="openFaq = openFaq === 4 ? null : 4"
                    class="w-full p-5 text-left font-extrabold text-base sm:text-lg text-ink flex items-center justify-between gap-4 hover:bg-slate-50 transition"
                    :aria-expanded="openFaq === 4"
                >
                    <span>Bagaimanakah guru boleh menyemak kemajuan murid?</span>
                    <span class="w-7 h-7 rounded-lg bg-sun border-2 border-ink flex items-center justify-center shrink-0 font-bold transition-transform duration-200" :class="openFaq === 4 ? 'rotate-45' : ''">+</span>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 text-sm sm:text-base text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Setiap modul mempunyai halaman 'Laporan Guru' automatik yang merumuskan tahap penguasaan murid mengikut topik, kadar ketepatan masa, dan cadangan penambahbaikan yang disediakan oleh ejen AI.
                </div>
            </div>
        </div>
    </section>

    <!-- Closing call to action -->
    <section class="academic-card p-8 sm:p-12 bg-sun border-3 border-ink shadow-pop-lg text-center relative overflow-hidden">
        <div class="relative z-10 max-w-2xl mx-auto space-y-5">
            <span class="sticker bg-white text-ink -rotate-2">Mula Belajar Sekarang</span>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-ink leading-tight">
                Bersedia Mengubah Cara Anda Mengulang Kaji?
            </h2>
            <p class="text-base sm:text-lg text-slate-800 font-medium leading-relaxed">
                Hanya muat naik nota anda dan biarkan AI mencipta pengalaman pembelajaran interaktif yang menyeronokkan.
            </p>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white text-base sm:text-lg px-8 py-4 rounded-2xl w-full sm:w-auto">
                    <span>Cipta Modul Pertama Percuma</span>
                </a>

                <form action="{{ route('decks.store') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="use_sample" value="1">
                    <button type="submit" class="btn-pop bg-white text-ink text-base px-6 py-4 rounded-2xl w-full sm:w-auto">
                        <span>Cuba Pek Contoh Segera (1-Klik)</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Sticky Floating Mascot Companion (Syed AI Assistant) -->
    <div
        class="fixed bottom-5 right-5 z-50 select-none"
        x-data="companionWidget()"
        x-show="scrolled"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-6 scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-6 scale-90"
        style="display: none;"
    >
        <!-- Speech Bubble Popup -->
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            @click.outside="isOpen = false"
            class="academic-card p-4 bg-white border-2 border-ink shadow-pop-lg w-72 sm:w-80 mb-3"
            style="display: none;"
        >
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    @include('partials.syed-avatar', ['class' => 'w-8 h-8'])
                    <span class="font-extrabold text-xs text-ink">Prof. Syed</span>
                </div>
                <button type="button" @click="isOpen = false" class="text-slate-400 hover:text-ink font-bold text-sm">✕</button>
            </div>
            <p class="mt-2.5 text-xs text-slate-700 leading-relaxed">
                "Ada dokumen nota atau teks subjek anda? Muat naik sekarang dan saya tukarkan jadi 9 aktiviti kuiz berkuasa AI!"
            </p>
            <div class="mt-3.5 flex flex-col gap-2">
                <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white text-xs py-2 w-full text-center">
                    Muat Naik Nota Baharu
                </a>
                <button
                    type="button"
                    @click="$dispatch('open-onboarding'); isOpen = false"
                    class="btn-pop bg-sun text-ink text-xs py-2 w-full"
                >
                    Panduan Langkah Demi Langkah
                </button>
            </div>
        </div>

        <!-- Floating Avatar Button -->
        <button
            type="button"
            @click="toggle()"
            class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-full shadow-pop hover:-translate-y-0.5 active:translate-y-0.5 transition-transform duration-200 flex items-center justify-center group"
            title="Buka pembantu Syed"
            aria-label="Buka pembantu Syed"
        >
            @include('partials.syed-avatar', ['class' => 'w-full h-full'])
            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-mint border-2 border-ink animate-pulse"></span>
            <span class="absolute -bottom-2 whitespace-nowrap bg-ink text-paper text-[10px] font-bold px-2 py-0.5 rounded-full border border-sun opacity-0 group-hover:opacity-100 transition-opacity">
                Tanya Syed
            </span>
        </button>
    </div>
</div>
@endsection
