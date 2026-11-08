import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Soft feedback sounds using the Web Audio API.
// Every tone is a sine wave through a low-pass filter with a short fade-in and a longer fade-out,
// so nothing clicks or pops. Ordinary UI clicks stay silent; only meaningful moments make a sound.
window.AcademicAudio = {
    enabled: true,
    ctx: null,
    output: null,

    init() {
        if (this.ctx || typeof window === 'undefined') {
            return;
        }

        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) {
            return;
        }

        this.ctx = new AudioCtx();

        const lowPass = this.ctx.createBiquadFilter();
        lowPass.type = 'lowpass';
        lowPass.frequency.value = 1600;
        lowPass.Q.value = 0.5;
        lowPass.connect(this.ctx.destination);
        this.output = lowPass;
    },

    /**
     * @param {number} frequency Start pitch in Hz.
     * @param {number} duration Length in seconds, including the fade-out.
     * @param {{ gain?: number, endFrequency?: number, delay?: number }} options
     */
    playTone(frequency, duration, { gain = 0.016, endFrequency = null, delay = 0 } = {}) {
        if (!this.enabled) {
            return;
        }

        try {
            this.init();
            if (!this.ctx) {
                return;
            }
            if (this.ctx.state === 'suspended') {
                this.ctx.resume();
            }

            const start = this.ctx.currentTime + delay;
            const fadeIn = 0.01;
            const osc = this.ctx.createOscillator();
            const envelope = this.ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(frequency, start);
            if (endFrequency) {
                osc.frequency.exponentialRampToValueAtTime(endFrequency, start + duration);
            }

            envelope.gain.setValueAtTime(0, start);
            envelope.gain.linearRampToValueAtTime(gain, start + fadeIn);
            envelope.gain.exponentialRampToValueAtTime(0.0001, start + duration);

            osc.connect(envelope);
            envelope.connect(this.output);
            osc.start(start);
            osc.stop(start + duration + 0.05);
        } catch (e) {
            // Audio is optional; ignore browsers that block or lack Web Audio.
        }
    },

    correct() {
        this.playTone(523.25, 0.22, { gain: 0.018 });
        this.playTone(659.25, 0.32, { gain: 0.018, delay: 0.09 });
    },

    incorrect() {
        this.playTone(180, 0.38, { gain: 0.02, endFrequency: 140 });
    },

    levelUp() {
        [392, 493.88, 587.33, 783.99].forEach((frequency, index) => {
            this.playTone(frequency, 0.34, { gain: 0.016, delay: index * 0.09 });
        });
    },

    complete() {
        [523.25, 659.25, 783.99].forEach((frequency, index) => {
            this.playTone(frequency, 0.4, { gain: 0.015, delay: index * 0.1 });
        });
    },

    // Ordinary interface sounds are intentionally silent.
    click() {},
    pop() {},
    flip() {},
};

// Mascot Hero Alpine Component
Alpine.data('mascotHero', () => ({
    tiltX: 0,
    tiltY: 0,
    lookX: 0,
    lookY: 0,
    isHovered: false,
    wiggleTimer: null,

    dialogue: "Hai! Saya Prof. Fahim, mentor AI anda. Klik saya atau cuba mini-kuiz di bawah.",
    isWiggling: false,
    sparkles: [],

    xp: 35,
    maxXp: 100,
    level: 1,
    streak: 0,
    levelTitle: 'Murid Cergas',

    quizIndex: 0,
    quizAnswered: false,
    quizFeedback: null,
    isCorrect: false,
    questions: [
        {
            q: "Klorofil menyerap spektrum cahaya hijau untuk fotosintesis?",
            a: false,
            expl: "Tepat! Daun hijau kerana klorofil MEMANTULKAN cahaya hijau dan menyerap merah & biru."
        },
        {
            q: "Fotosintesis menghasilkan gas Oksigen (O₂) dan Glukosa?",
            a: true,
            expl: "Tepat sekali! Formula: 6CO₂ + 6H₂O → C₆H₁₂O₆ + 6O₂."
        },
        {
            q: "Mitokondria ialah organel yang menjana tenaga (ATP) sel?",
            a: true,
            expl: "Hebat! Mitokondria dipanggil pusat jana kuasa sel."
        },
        {
            q: "KwizLoo menukar nota PDF kepada 9 mod kuiz secara automatik?",
            a: true,
            expl: "Betul! AI kami mengekstrak konsep teras dalam beberapa saat sahaja."
        }
    ],

    isFlipped: false,
    flashcardIndex: 0,
    flashcards: [
        {
            term: "Fotolisis Air",
            category: "Biologi SPM",
            def: "Penguraian molekul air (H₂O) kepada ion hidrogen dan oksigen oleh cahaya dalam tindak balas bersandarkan cahaya."
        },
        {
            term: "Osmosis",
            category: "Fisiologi Sel",
            def: "Pergerakan bersih molekul air merentasi membran separa telap dari keupayaan air tinggi ke rendah."
        },
        {
            term: "Enzim",
            category: "Biokimia",
            def: "Mangkin biologi protein yang mempercepatkan kadar tindak balas dengan merendahkan tenaga pengaktifan."
        }
    ],

    quotes: [
        "Hai! Saya Prof. Fahim, pembantu AI anda. Muat naik nota, biar saya sediakan kuiz A+!",
        "Petua: Mengulang kaji 15 minit secara aktif 3x lebih berkesan daripada membaca tanpa henti!",
        "Cuba jawab soalan mini-kuiz di bawah saya.",
        "Kad imbas di bawah boleh diterbalikkan. Klik untuk uji ingatan anda.",
        "Setiap latihan kecil membantu otak mengukuhkan sambungan ingatan sinaps!",
        "Sedia untuk peperiksaan? Klik 'Mulakan Sekarang' untuk cuba pek contoh segera!"
    ],
    quoteIndex: 0,

    handleMouseMove(e) {
        const rect = this.$refs.stage?.getBoundingClientRect() || this.$el.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        const relX = Math.max(-1, Math.min(1, x / (rect.width / 2)));
        const relY = Math.max(-1, Math.min(1, y / (rect.height / 2)));
        this.tiltX = -relY * 5;
        this.tiltY = relX * 5;
        this.lookX = relX * 3;
        this.lookY = relY * 2;
        this.isHovered = true;
    },

    handleMouseLeave() {
        this.tiltX = 0;
        this.tiltY = 0;
        this.lookX = 0;
        this.lookY = 0;
        this.isHovered = false;
    },

    petMascot() {
        clearTimeout(this.wiggleTimer);
        this.isWiggling = false;
        requestAnimationFrame(() => {
            this.isWiggling = true;
        });
        this.addXp(15);
        window.AcademicAudio?.pop();
        this.spawnSparkle();

        this.quoteIndex = (this.quoteIndex + 1) % this.quotes.length;
        this.dialogue = this.quotes[this.quoteIndex];

        this.wiggleTimer = setTimeout(() => {
            this.isWiggling = false;
        }, 750);
    },

    spawnSparkle(label = '+15 XP') {
        const id = Date.now() + Math.random();
        const x = 20 + Math.random() * 60;
        const y = 20 + Math.random() * 40;
        this.sparkles.push({ id, x, y, label });
        setTimeout(() => {
            this.sparkles = this.sparkles.filter(s => s.id !== id);
        }, 950);
    },

    answerQuiz(userChoice) {
        if (this.quizAnswered) return;
        const current = this.questions[this.quizIndex];
        const correct = userChoice === current.a;
        this.quizAnswered = true;
        this.isCorrect = correct;

        if (correct) {
            this.streak++;
            this.addXp(25);
            window.AcademicAudio?.correct();
            this.quizFeedback = current.expl;
            this.dialogue = "Syabas! Jawapan anda tepat sekali. Teruskan momentum!";
            this.spawnSparkle('+25 XP');
        } else {
            this.streak = 0;
            this.addXp(5);
            window.AcademicAudio?.incorrect();
            this.quizFeedback = `Kurang tepat. ${current.expl}`;
            this.dialogue = "Jangan bimbang! Kesilapan ialah langkah pertama untuk kuasai topik.";
        }
    },

    nextQuiz() {
        window.AcademicAudio?.click();
        this.quizAnswered = false;
        this.quizFeedback = null;
        this.quizIndex = (this.quizIndex + 1) % this.questions.length;
    },

    toggleFlip() {
        this.isFlipped = !this.isFlipped;
        window.AcademicAudio?.flip();
        if (this.isFlipped) {
            this.addXp(10);
            this.dialogue = "Bagus! Kaedah 'Active Recall' dengan kad imbas menguatkan ingatan SPM!";
        }
    },

    nextFlashcard() {
        window.AcademicAudio?.click();
        this.isFlipped = false;
        setTimeout(() => {
            this.flashcardIndex = (this.flashcardIndex + 1) % this.flashcards.length;
        }, 200);
    },

    addXp(amount) {
        this.xp += amount;
        if (this.xp >= this.maxXp) {
            this.xp = this.xp - this.maxXp;
            this.level++;
            this.maxXp = Math.round(this.maxXp * 1.4);
            window.AcademicAudio?.levelUp();
            this.updateLevelTitle();
            this.dialogue = `Tahniah! Anda naik ke Tahap ${this.level} (${this.levelTitle})!`;
        }
    },

    updateLevelTitle() {
        if (this.level === 1) this.levelTitle = "Murid Cergas";
        else if (this.level === 2) this.levelTitle = "Pencari Ilmu";
        else if (this.level === 3) this.levelTitle = "Sarjana Muda";
        else if (this.level === 4) this.levelTitle = "Pendeta Minda";
        else this.levelTitle = "Profesor Ulung";
    }
}));

// Floating Companion Alpine Component
Alpine.data('companionWidget', () => ({
    isOpen: false,
    scrolled: false,

    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 400;
        }, { passive: true });
    },

    toggle() {
        this.isOpen = !this.isOpen;
        window.AcademicAudio?.click();
    }
}));

// Global helper for secure POST fetch requests with CSRF token
window.secureFetch = async function(url, data) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token || '',
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || 'Ralat komunikasi dengan pelayan.');
    }

    return response.json();
};

Alpine.start();
