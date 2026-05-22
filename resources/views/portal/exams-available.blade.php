@extends('layouts.app')

@section('title', 'Available Exams - TOPCIT')

@section('content')
<div class="min-h-screen bg-dark">
    <nav class="sticky top-0 z-30 bg-dark/80 backdrop-blur-lg border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl">🎓</span>
                <span class="font-bold gradient-text">TOPCIT</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ url('/portal/dashboard') }}" class="text-xs text-gray-400 hover:text-white transition-colors">Dashboard</a>
                <a href="{{ url('/portal/logout') }}" class="text-xs text-gray-500 hover:text-danger transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <h1 class="page-header" data-aos="fade-right">📋 Available Examinations</h1>

        <div class="space-y-4" data-aos="fade-up">
            @forelse($examinations as $exam)
                <div class="card card-hover">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-200">{{ $exam->title }}</h3>
                            <p class="text-sm text-gray-400 mt-1">{{ $exam->description ?? 'No description' }}</p>
                            <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500">
                                <span>📝 {{ $exam->limit }} questions</span>
                                <span>⏱ {{ $exam->duration }} minutes</span>
                                @if($exam->examination_at)
                                    <span>📅 {{ date('M d, Y h:i A', strtotime($exam->examination_at)) }}</span>
                                @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ url('/portal/examinations/attempt/') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $exam->id }}">
                            <button type="submit" class="btn-primary text-sm whitespace-nowrap">Start Exam</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="card text-center py-12">
                    <span class="text-4xl block mb-3">📭</span>
                    <p class="text-gray-400">No examinations available at the moment.</p>
                    <p class="text-xs text-gray-500 mt-1">Check back later for new exams.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection