@extends('layouts.admin')

@section('title', 'Professors - TOPCIT')
@section('page-title', 'Professors')

@section('admin-content')
<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Professors</h3>
        <button @@click="$dispatch('open-modal', 'addProfessor')" class="btn-primary text-xs py-1.5 px-3">+ Add Professor</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="professors-table">
            <thead>
                <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                    <th class="text-left py-3 px-2">Employee No</th>
                    <th class="text-left py-3 px-2">Name</th>
                    <th class="text-left py-3 px-2">Email</th>
                    <th class="text-left py-3 px-2">Contact</th>
                    <th class="text-left py-3 px-2">Status</th>
                    <th class="text-left py-3 px-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($professors as $prof)
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 px-2 font-mono text-xs">{{ $prof->employee_no }}</td>
                    <td class="py-3 px-2">{{ $prof->surname }}, {{ $prof->firstname }}</td>
                    <td class="py-3 px-2 text-gray-400">{{ $prof->email }}</td>
                    <td class="py-3 px-2 text-gray-400">{{ $prof->contact }}</td>
                    <td class="py-3 px-2">
                        <span class="text-xs {{ $prof->status ? 'text-success' : 'text-danger' }}">
                            {{ $prof->status_text ?? ($prof->status ? 'Active' : 'Inactive') }}
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
        new DataTable('#professors-table', {
            pageLength: 25,
            order: [[1, 'asc']],
        });
    });
</script>
@endpush