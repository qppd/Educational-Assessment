<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result — {{ $examinations[0]->title ?? 'Exam' }} — TOPCIT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-gray-100 font-sans antialiased min-h-screen">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8" data-aos="fade-down">
            <span class="text-5xl block mb-4">{{ $correctCount >= ($wrongCount + $correctCount) / 2 ? '🎉' : '💪' }}</span>
            <h1 class="text-3xl font-extrabold gradient-text">Exam Complete!</h1>
            <p class="text-gray-400 mt-2">{{ $examinations[0]->title ?? 'Examination' }}</p>
        </div>

        <!-- Score Card -->
        <div class="card text-center mb-8" data-aos="fade-up">
            <div class="flex justify-center mb-4">
                <svg class="w-32 h-32" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="8"/>
                    <circle cx="60" cy="60" r="54" fill="none"
                            stroke="url(#grad)" stroke-width="8"
                            stroke-linecap="round"
                            stroke-dasharray="339.292"
                            stroke-dashoffset="{{ 339.292 - (339.292 * ($correctCount / max($correctCount + $wrongCount, 1))) }}"
                            transform="rotate(-90, 60, 60)"
                            style="transition: stroke-dashoffset 1.5s ease"/>
                    <defs>
                        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#6366F1"/>
                            <stop offset="100%" stop-color="#06B6D4"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div class="text-4xl font-extrabold gradient-text mb-1">{{ number_format(($correctCount / max($correctCount + $wrongCount, 1)) * 100, 0) }}%</div>
            <p class="text-sm text-gray-400">
                <span class="text-success font-medium">{{ $correctCount }} correct</span>
                <span class="mx-2">•</span>
                <span class="text-danger font-medium">{{ $wrongCount }} wrong</span>
                <span class="mx-2">•</span>
                <span class="text-gray-500">{{ $correctCount + $wrongCount }} total</span>
            </p>
        </div>

        <!-- Results Breakdown -->
        @if(isset($results) && count($results) > 0)
        <div class="space-y-3" data-aos="fade-up" data-aos-delay="100">
            <h3 class="section-title">📋 Detailed Results</h3>
            @foreach($results as $index => $result)
                <div class="card p-4 flex items-start gap-3">
                    <div class="text-lg flex-shrink-0 mt-0.5">
                        {{ $result->result === 'correct' ? '✅' : '❌' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-200 mb-1.5">Question {{ $index + 1 }}</p>
                        <p class="text-xs text-gray-400 mb-2">
                            Your answer: <span class="{{ $result->result === 'correct' ? 'text-success' : 'text-danger' }}">{{ $result->student_answer ?? '(no answer)' }}</span>
                        </p>
                        @if($result->result === 'wrong')
                            <p class="text-xs text-gray-500">
                                Correct: <span class="text-success">{{ $result->correct_answer }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-8" data-aos="fade-up">
            <a href="{{ url('/portal/dashboard') }}" class="btn-primary inline-flex items-center gap-2">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>