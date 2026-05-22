@extends('layouts.admin')

@section('title', 'Examinations - TOPCIT')
@section('page-title', 'Examinations')

@section('admin-content')
<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Examinations</h3>
        <button @@click="$dispatch('open-modal', 'addExamination')" class="btn-primary text-xs py-1.5 px-3">+ New Exam</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="examinations-table">
            <thead>
                <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                    <th class="text-left py-3 px-2">ID</th>
                    <th class="text-left py-3 px-2">Title</th>
                    <th class="text-left py-3 px-2">Questions</th>
                    <th class="text-left py-3 px-2">Duration</th>
                    <th class="text-left py-3 px-2">Status</th>
                    <th class="text-left py-3 px-2">Schedule</th>
                    <th class="text-left py-3 px-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examinations as $exam)
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 px-2 font-mono text-xs">{{ $exam->id }}</td>
                    <td class="py-3 px-2 font-medium">{{ $exam->title }}</td>
                    <td class="py-3 px-2 text-gray-400">{{ $exam->question_count ?? 0 }}</td>
                    <td class="py-3 px-2 text-gray-400">{{ $exam->duration }} min</td>
                    <td class="py-3 px-2"><x-badge :status="$exam->statusInt ?? $exam->status" /></td>
                    <td class="py-3 px-2 text-xs text-gray-400">{{ $exam->examination_at ? date('M d, h:i A', strtotime($exam->examination_at)) : '—' }}</td>
                    <td class="py-3 px-2">
                        <div class="flex gap-2">
                            <a href="{{ url('/admin/examinations/manage/' . $exam->id) }}" class="text-xs text-primary hover:text-secondary transition-colors">Manage</a>
                            <a href="{{ url('/admin/examinations/reviewers/' . $exam->id) }}" class="text-xs text-gray-400 hover:text-white transition-colors">Reviewers</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('datatables')
<script>
    $(document).ready(function() {
        new DataTable('#examinations-table', {
            pageLength: 25,
            order: [[0, 'desc']],
        });
    });
</script>
@endpush