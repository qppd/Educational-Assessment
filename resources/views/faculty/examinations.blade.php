@extends('layouts.admin')

@section('title', 'Examinations - Faculty - TOPCIT')
@section('page-title', 'Examinations')

@section('admin-content')
    <div class="card" data-aos="fade-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title mb-0">All Examinations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="examinations-table">
                <thead>
                    <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                        <th class="text-left py-3 px-2">ID</th>
                        <th class="text-left py-3 px-2">Title</th>
                        <th class="text-left py-3 px-2">Duration</th>
                        <th class="text-left py-3 px-2">Questions</th>
                        <th class="text-left py-3 px-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($examinations as $exam)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="py-3 px-2 font-mono text-xs">{{ $exam->id }}</td>
                        <td class="py-3 px-2 font-medium">{{ $exam->title }}</td>
                        <td class="py-3 px-2 text-gray-400">{{ $exam->duration }} min</td>
                        <td class="py-3 px-2 text-gray-400">{{ $exam->question_count ?? 0 }}</td>
                        <td class="py-3 px-2"><x-badge :status="$exam->status ?? $exam->statusInt" /></td>
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