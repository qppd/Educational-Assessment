<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $examination[0]->title ?? 'Exam' }} — TOPCIT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #0F172A; font-family: 'Inter', sans-serif; }
        .question-card { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .option-card { cursor: pointer; transition: all 0.2s ease; }
        .option-card:hover { border-color: #6366F1; background: rgba(99,102,241,0.1); }
        .option-card.selected { border-color: #6366F1; background: rgba(99,102,241,0.15); box-shadow: 0 0 20px rgba(99,102,241,0.15); }
        .nav-btn { transition: all 0.2s ease; }
        .nav-btn.active { background: #6366F1; color: white; }
        .nav-btn.answered { background: rgba(16,185,129,0.2); border-color: #10B981; color: #10B981; }
        .nav-btn.skipped { background: rgba(239,68,68,0.1); border-color: #EF4444; color: #EF4444; }
    </style>
</head>
<body x-data="examApp()" x-init="init()" class="min-h-screen">
    <!-- Top Bar -->
    <div class="sticky top-0 z-40 bg-surface/95 backdrop-blur-lg border-b border-white/5">
        <div class="flex items-center justify-between px-4 py-3 max-w-6xl mx-auto">
            <div class="flex items-center gap-3">
                <span class="text-lg">🎓</span>
                <span class="font-semibold text-sm text-gray-200 truncate max-w-[200px] sm:max-w-none">
                    {{ $examination[0]->title ?? 'Examination' }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">Time Left:</span>
                    <span id="exam-timer" class="font-mono text-lg font-bold text-gray-200 w-16 text-right"
                          x-text="formatTime(timeLeft)"></span>
                </div>
                <button @@click="submitExam()" class="btn-success text-xs py-1.5 px-3">Submit</button>
            </div>
        </div>
    </div>

    <!-- Resume Banner -->
    <div x-show="showResumeBanner"
         class="max-w-6xl mx-auto px-4 pt-4"
         x-cloak>
        <div class="flex items-center justify-between bg-primary/20 border border-primary/30 rounded-lg px-4 py-3 text-sm">
            <span class="text-primary-light">📋 Resumed from previous session — answers restored</span>
            <button @@click="showResumeBanner = false"
                    class="text-gray-400 hover:text-white ml-4 flex-shrink-0">
                <span class="text-lg leading-none">&times;</span>
            </button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-6 flex gap-6">
        <!-- Question Area -->
        <div class="flex-1 min-w-0">
            <template x-for="(q, i) in questions" :key="q.id">
                <div x-show="currentQuestion === i" class="question-card">
                    <div class="card">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs text-gray-500">Question <span x-text="i + 1"></span> of <span x-text="questions.length"></span></span>
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  :class="q.type === 'Multiple choice' || q.type === 0 ? 'bg-primary/20 text-primary' : (q.type === 'Enumeration' || q.type === 1 ? 'bg-secondary/20 text-secondary' : 'bg-accent/20 text-amber-400')"
                                  x-text="typeof q.type === 'string' ? q.type : (q.type === 0 ? 'Multiple Choice' : q.type === 1 ? 'Enumeration' : 'Fill in the Blank')"></span>
                        </div>

                        <!-- Question Text -->
                        <div class="prose prose-invert max-w-none mb-6 text-sm leading-relaxed"
                             x-html="q.question"></div>

                        <!-- Multiple Choice -->
                        <template x-if="q.type === 0 || q.type === 'Multiple choice'">
                            <div class="space-y-3">
                                <template x-for="(choice, ci) in getChoices(q)" :key="ci">
                                    <div class="option-card border border-white/10 rounded-lg p-3 flex items-center gap-3"
                                         :class="answers[q.id] === choice ? 'selected' : ''"
                                         @@click="selectAnswer(q.id, choice)">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                             :class="answers[q.id] === choice ? 'border-primary bg-primary' : 'border-white/20'">
                                            <div x-show="answers[q.id] === choice" class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                        </div>
                                        <span class="text-sm text-gray-200" x-text="choice"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Enumeration -->
                        <template x-if="q.type === 1 || q.type === 'Enumeration'">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Your Answer:</label>
                                <input type="text" class="input-field"
                                       x-model="answers[q.id]"
                                       @@input="autoSave()"
                                       placeholder="Type your answer here...">
                            </div>
                        </template>

                        <!-- Fill in the blank -->
                        <template x-if="q.type === 2 || q.type === 'Fill in the blank'">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Fill in the blank:</label>
                                <input type="text" class="input-field"
                                       x-model="answers[q.id]"
                                       @@input="autoSave()"
                                       placeholder="Type your answer here...">
                            </div>
                        </template>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-4">
                        <button @@click="prevQuestion()" x-show="currentQuestion > 0"
                                class="btn-secondary text-sm">← Previous</button>
                        <div x-show="!currentQuestion" class="invisible"><button class="btn-secondary text-sm">← Previous</button></div>
                        <button @@click="nextQuestion()" x-show="currentQuestion < questions.length - 1"
                                class="btn-primary text-sm">Next →</button>
                        <button @@click="submitExam()" x-show="currentQuestion === questions.length - 1"
                                class="btn-success text-sm">✓ Submit Exam</button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Question Navigator -->
        <div class="w-20 sm:w-24 flex-shrink-0">
            <div class="sticky top-20 glass-strong p-3 rounded-xl">
                <p class="text-xs text-gray-400 text-center mb-3">Questions</p>
                <div class="grid grid-cols-4 gap-1.5">
                    <template x-for="(q, i) in questions" :key="i">
                        <button @@click="currentQuestion = i"
                                class="nav-btn w-7 h-7 rounded-md text-xs font-medium border border-white/10 flex items-center justify-center"
                                :class="{
                                    'active': currentQuestion === i,
                                    'answered': answers[q.id] && answers[q.id].trim(),
                                    'skipped': currentQuestion !== i && (!answers[q.id] || !answers[q.id].trim())
                                }"
                                x-text="i + 1">
                        </button>
                    </template>
                </div>
                <div class="mt-3 pt-3 border-t border-white/5 space-y-1 text-xs text-gray-500">
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-primary"></span> Current</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-success"></span> Answered</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-danger/50"></span> Skipped</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Modal -->
    <div x-show="showSubmitModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" @@keydown.escape.window="showSubmitModal = false">
        <div class="glass-strong rounded-xl p-6 max-w-md w-full" @@click.outside="showSubmitModal = false">
            <h3 class="text-lg font-semibold mb-2">Submit Examination?</h3>
            <p class="text-sm text-gray-400 mb-4">
                You have <span class="text-danger font-bold" x-text="skippedCount"></span> unanswered question(s).
                <span x-show="skippedCount > 0">Are you sure you want to submit?</span>
            </p>
            <div class="flex gap-3 justify-end">
                <button @@click="showSubmitModal = false" class="btn-secondary text-sm">Review</button>
                <button @@click="confirmSubmit()" class="btn-success text-sm">Submit Anyway</button>
            </div>
        </div>
    </div>

    <!-- Hidden form for submission -->
    <form id="submit-form" method="POST" action="{{ url('/portal/examination/submit') }}" class="hidden">
        @csrf
        <input type="hidden" name="examination_id" value="{{ $examination[0]->id ?? '' }}">
        <input type="hidden" name="answers_data" id="answers_data">
        <input type="hidden" name="question_ids" id="question_ids">
    </form>

    <script>
    function examApp() {
        return {
            questions: @json($questions ?? []),
            answers: {},
            currentQuestion: 0,
            timeLeft: {{ ($examination[0]->duration ?? 10) * 60 }},
            duration: {{ ($examination[0]->duration ?? 10) * 60 }},
            showSubmitModal: false,
            autoSaveTimer: null,
            timerSaveInterval: null,
            showResumeBanner: false,

            get skippedCount() {
                return this.questions.filter(q => !this.answers[q.id] || !this.answers[q.id].trim()).length;
            },

            init() {
                // Restore saved answers from localStorage
                const saved = localStorage.getItem('exam_answers_{{ $examination[0]->id ?? 0 }}');
                if (saved) {
                    try {
                        this.answers = JSON.parse(saved);
                        // Show resume banner
                        this.showResumeBanner = true;
                    } catch(e) {}
                }
                // Restore saved timer from localStorage
                const savedTimer = localStorage.getItem('exam_timer_{{ $examination[0]->id ?? 0 }}');
                if (savedTimer) {
                    try {
                        const parsed = parseInt(savedTimer, 10);
                        if (!isNaN(parsed) && parsed > 0 && parsed <= this.duration) {
                            this.timeLeft = parsed;
                        }
                    } catch(e) {}
                }
                // Automatically resume: find first unanswered question
                if (saved) {
                    const firstUnanswered = this.questions.findIndex(q => !this.answers[q.id] || !this.answers[q.id].trim());
                    if (firstUnanswered !== -1) {
                        this.currentQuestion = firstUnanswered;
                    } else {
                        this.currentQuestion = 0;
                    }
                }
                // Start timer
                this.startTimer();
                // Auto-save every 30s, plus save timer to localStorage every 10s
                this.autoSaveTimer = setInterval(() => this.autoSave(), 30000);
                this.timerSaveInterval = setInterval(() => {
                    localStorage.setItem('exam_timer_{{ $examination[0]->id ?? 0 }}', this.timeLeft);
                }, 10000);
                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                    if (e.key === 'ArrowRight') this.nextQuestion();
                    if (e.key === 'ArrowLeft') this.prevQuestion();
                    if (['1','2','3','4'].includes(e.key)) {
                        const q = this.questions[this.currentQuestion];
                        if (q && (q.type === 0 || q.type === 'Multiple choice')) {
                            const choices = this.getChoices(q);
                            if (choices[parseInt(e.key) - 1]) {
                                this.selectAnswer(q.id, choices[parseInt(e.key) - 1]);
                            }
                        }
                    }
                });
            },

            getChoices(q) {
                const c = [];
                if (q.choice_1) c.push(q.choice_1);
                if (q.choice_2) c.push(q.choice_2);
                if (q.choice_3) c.push(q.choice_3);
                if (q.choice_4) c.push(q.choice_4);
                return c;
            },

            selectAnswer(questionId, answer) {
                this.answers[questionId] = answer;
                this.autoSave();
            },

            autoSave() {
                localStorage.setItem('exam_answers_{{ $examination[0]->id ?? 0 }}', JSON.stringify(this.answers));
            },

            startTimer() {
                this.timer = setInterval(() => {
                    this.timeLeft--;
                    if (this.timeLeft <= 0) {
                        clearInterval(this.timer);
                        this.confirmSubmit();
                    }
                }, 1000);
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            },

            nextQuestion() {
                if (this.currentQuestion < this.questions.length - 1) {
                    this.currentQuestion++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevQuestion() {
                if (this.currentQuestion > 0) {
                    this.currentQuestion--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            submitExam() {
                this.showSubmitModal = true;
            },

            confirmSubmit() {
                clearInterval(this.timer);
                clearInterval(this.autoSaveTimer);
                clearInterval(this.timerSaveInterval);
                // Populate hidden form
                const questionIds = this.questions.map(q => q.id);
                document.getElementById('question_ids').value = JSON.stringify(questionIds);
                document.getElementById('answers_data').value = JSON.stringify(this.answers);
                localStorage.removeItem('exam_answers_{{ $examination[0]->id ?? 0 }}');
                localStorage.removeItem('exam_timer_{{ $examination[0]->id ?? 0 }}');
                document.getElementById('submit-form').submit();
            }
        }
    }
    </script>
</body>
</html>