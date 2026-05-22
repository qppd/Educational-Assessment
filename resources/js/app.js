import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';

window.Alpine = Alpine;
Alpine.start();

// Initialize AOS animations
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic',
    });
});

// Preloader
window.addEventListener('load', () => {
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        setTimeout(() => preloader.classList.add('fade-out'), 200);
    }
});

// Particle background for landing page
function createParticles(container) {
    if (!container) return;
    for (let i = 0; i < 30; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.left = Math.random() * 100 + '%';
        p.style.animationDuration = (8 + Math.random() * 12) + 's';
        p.style.animationDelay = (Math.random() * 10) + 's';
        p.style.width = p.style.height = (2 + Math.random() * 4) + 'px';
        container.appendChild(p);
    }
}
window.createParticles = createParticles;

// Exam timer
class ExamTimer {
    constructor(durationMinutes, onTimeUp) {
        this.totalSeconds = durationMinutes * 60;
        this.onTimeUp = onTimeUp;
        this.interval = null;
    }
    start() {
        this.update();
        this.interval = setInterval(() => {
            this.totalSeconds--;
            this.update();
            if (this.totalSeconds <= 0) {
                clearInterval(this.interval);
                if (this.onTimeUp) this.onTimeUp();
            }
        }, 1000);
    }
    update() {
        const el = document.getElementById('exam-timer');
        if (!el) return;
        const mins = Math.floor(this.totalSeconds / 60);
        const secs = this.totalSeconds % 60;
        el.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        if (this.totalSeconds < 300) el.classList.add('text-danger', 'animate-pulse');
    }
    stop() { clearInterval(this.interval); }
}
window.ExamTimer = ExamTimer;
