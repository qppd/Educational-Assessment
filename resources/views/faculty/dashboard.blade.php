@extends('layouts.admin')

@section('title', 'Faculty Dashboard - TOPCIT')
@section('page-title', 'Faculty Dashboard')

@section('admin-content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8" data-aos="fade-up">
        <x-stat-card icon="👨‍🎓" label="Students" :value="$students[0]->students_count ?? 0" />
        <x-stat-card icon="📝" label="Examinations" :value="$examinations[0]->examinations_count ?? 0" />
        <x-stat-card icon="❓" label="Questions" :value="$questions[0]->questions_count ?? 0" />
    </div>

    <div class="card" data-aos="fade-up" data-aos-delay="100">
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
                        <span class="text-xs font-bold gradient-text">{{ number_format($student->average_score ?? 0, 1) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-8">No student data yet</p>
        @endif
    </div>
@endsection