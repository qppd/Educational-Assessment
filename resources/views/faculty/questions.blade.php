@extends('layouts.admin')

@section('title', 'Questions - TOPCIT')
@section('page-title', 'Questions | {{ $examination->title ?? "" }}')

@section('admin-content')
<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">Questions</h3>
        <button @@click="$dispatch('open-modal', 'request')" class="btn-primary text-xs py-1.5 px-3">+ Request Question</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="questions-table">
            <thead>
                <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                    <th class="text-left py-3 px-2">#</th>
                    <th class="text-left py-3 px-2">Question</th>
                    <th class="text-left py-3 px-2">Type</th>
                    <th class="text-left py-3 px-2">Answer</th>
                    <th class="text-left py-3 px-2">Status</th>
                    <th class="text-left py-3 px-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $q)
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 px-2 font-mono text-xs">{{ $q->id }}</td>
                    <td class="py-3 px-2 max-w-xs truncate">{!! $q->question !!}</td>
                    <td class="py-3 px-2 text-gray-400">{{ $q->type == 0 ? 'MCQ' : ($q->type == 1 ? 'Enum' : 'Fill') }}</td>
                    <td class="py-3 px-2 text-success text-xs">{{ $q->answer }}</td>
                    <td class="py-3 px-2"><x-badge :status="$q->status" /></td>
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
        new DataTable('#questions-table', {
            pageLength: 25,
            order: [[0, 'asc']],
        });
    });
</script>
@endpush