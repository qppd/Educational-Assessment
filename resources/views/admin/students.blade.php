@extends('layouts.admin')

@section('title', 'Students - TOPCIT')
@section('page-title', 'Students')

@section('admin-content')
<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Students</h3>
        <div class="flex gap-2">
            <a href="{{ url('/admin/student/export') }}" class="btn-secondary text-xs py-1.5 px-3">Export Template</a>
            <button @@click="$dispatch('open-modal', 'addStudent')" class="btn-primary text-xs py-1.5 px-3">+ Add Student</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="students-table">
            <thead>
                <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                    <th class="text-left py-3 px-2">Student No</th>
                    <th class="text-left py-3 px-2">Name</th>
                    <th class="text-left py-3 px-2">Contact</th>
                    <th class="text-left py-3 px-2">Email</th>
                    <th class="text-left py-3 px-2">Account</th>
                    <th class="text-left py-3 px-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 px-2 font-mono text-xs">{{ $student->student_no }}</td>
                    <td class="py-3 px-2">{{ $student->lastname }}, {{ $student->firstname }}</td>
                    <td class="py-3 px-2 text-gray-400">{{ $student->contact ?? '—' }}</td>
                    <td class="py-3 px-2 text-gray-400">{{ $student->email ?? '—' }}</td>
                    <td class="py-3 px-2">
                        <span class="text-xs {{ $student->account_status === 'with account' ? 'text-success' : 'text-gray-500' }}">
                            {{ $student->account_status }}
                        </span>
                    </td>
                    <td class="py-3 px-2">
                        <button class="text-xs text-primary hover:text-secondary transition-colors">Edit</button>
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
        new DataTable('#students-table', {
            pageLength: 25,
            order: [[0, 'asc']],
        });
    });
</script>
@endpush