@extends('layouts.app')

@section('title', 'Dashboard - TOPCIT')

@section('content')
<div class="min-h-screen bg-dark">
    <!-- TOPCIT Navbar -->
    <nav class="sticky top-0 z-30 bg-dark/80 backdrop-blur-lg border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl">🎓</span>
                <span class="font-bold gradient-text">TOPCIT</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400 hidden sm:inline">{{ session('name') }}</span>
                <a href="{{ url('/portal/logout') }}"
                   class="text-xs text-gray-500 hover:text-danger transition-colors px-3 py-1.5 rounded-lg hover:bg-white/5">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" data-aos="fade-up">
            <x-stat-card icon="📝" label="Available Exams" :value="$stats['available'] ?? 0" />
            <x-stat-card icon="✅" label="Taken Exams" :value="$stats['taken'] ?? 0" />
            <x-stat-card icon="🏆" label="Your Rank" :value="$stats['rank'] ?? '—'" />
            <x-stat-card icon="📊" label="Avg Score" :value="$stats['avg_score'] ?? 0" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions / Available Exams -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card" data-aos="fade-up">
                    <h3 class="section-title">📋 Available Examinations</h3>
                    @if(isset($availableExams) && count($availableExams) > 0)
                        <div class="space-y-3">
                            @foreach($availableExams as $exam)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-medium text-gray-200 truncate">{{ $exam->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $exam->limit }} questions • {{ $exam->duration }} min
                                            @if($exam->examination_at) • {{ date('M d, h:i A', strtotime($exam->examination_at)) }} @endif
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ url('/portal/examinations/attempt/') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $exam->id }}">
                                        <button type="submit" class="btn-primary text-xs py-1.5 px-3">Take Exam</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-8">No exams available right now</p>
                    @endif
                </div>

                <!-- Taken Exams -->
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="section-title">✅ Taken Examinations</h3>
                    @if(isset($takenExams) && count($takenExams) > 0)
                        <div class="space-y-3">
                            @foreach($takenExams as $exam)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-medium text-gray-200 truncate">{{ $exam->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">Completed</p>
                                    </div>
                                    <a href="{{ url('/portal/examinations/result/' . $exam->id) }}"
                                       class="btn-secondary text-xs py-1.5 px-3">View Result</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-8">No exams taken yet</p>
                    @endif
                </div>
            </div>

            <!-- Leaderboard Sidebar -->
            <div class="space-y-6">
                <div class="card" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="section-title">🏆 Leaderboard</h3>
                    @if(isset($rankedStudents) && count($rankedStudents) > 0)
                        <div class="space-y-2">
                            @foreach($rankedStudents as $index => $student)
                                <div class="flex items-center gap-3 p-2 rounded-lg {{ $index === 0 ? 'bg-amber-500/10' : ($index < 3 ? 'bg-white/5' : '') }}">
                                    <span class="w-6 text-center text-sm font-bold
                                        {{ $index === 0 ? 'text-amber-400' : ($index === 1 ? 'text-gray-300' : ($index === 2 ? 'text-amber-600' : 'text-gray-500')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="w-7 h-7 rounded-full bg-primary/30 flex items-center justify-center text-xs font-medium">
                                        {{ substr($student->firstname, 0, 1) }}{{ substr($student->surname, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-200 truncate">{{ $student->surname }}, {{ $student->firstname }}</p>
                                    </div>
                                    <span class="text-xs font-bold gradient-text">{{ number_format($student->percentage_score ?? $student->average_score ?? 0, 1) }}%</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-8">No rankings yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection