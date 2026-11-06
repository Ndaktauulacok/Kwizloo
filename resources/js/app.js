import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Academic audio cues using Web Audio API
window.AcademicAudio = {
    enabled: true,
    ctx: null,

    init() {
        if (!this.ctx && typeof window !== 'undefined') {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                this.ctx = new AudioCtx();
            }
        }
    },

    playTone(frequency, duration, type = 'sine', gainVal = 0.04) {
        if (!this.enabled) return;
        try {
            this.init();
            if (!this.ctx) return;
            if (this.ctx.state === 'suspended') {
                this.ctx.resume();
            }
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();
            osc.type = type;
            osc.frequency.setValueAtTime(frequency, this.ctx.currentTime);
            gain.gain.setValueAtTime(gainVal, this.ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.0001, this.ctx.currentTime + duration);
            osc.connect(gain);
            gain.connect(this.ctx.destination);
            osc.start();
            osc.stop(this.ctx.currentTime + duration);
        } catch (e) {
            // Audio context failed or blocked by policy
        }
    },

    correct() {
        this.playTone(523.25, 0.12, 'triangle', 0.05); // C5
        setTimeout(() => this.playTone(659.25, 0.18, 'triangle', 0.05), 80); // E5
    },

    incorrect() {
        this.playTone(220, 0.18, 'sawtooth', 0.03); // Low A3
    },

    complete() {
        [523.25, 659.25, 783.99, 1046.5].forEach((freq, idx) => {
            setTimeout(() => this.playTone(freq, 0.16, 'sine', 0.05), idx * 75);
        });
    }
};

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
