@extends('layouts.admin')

@section('title', 'Administrators - TOPCIT')
@section('page-title', 'Administrators')

@section('admin-content')
<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Administrators</h3>
        <button @@click="$dispatch('open-modal', 'addAdministrator')" class="btn-primary text-xs py-1.5 px-3">
            + Add Admin
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="administrators-table">
            <thead>
                <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                    <th class="text-left py-3 px-2">Name</th>
                    <th class="text-left py-3 px-2">Email</th>
                    <th class="text-left py-3 px-2">Role</th>
                    <th class="text-left py-3 px-2">Status</th>
                    <th class="text-left py-3 px-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($administrators as $admin)
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 px-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary/30 flex items-center justify-center text-xs font-medium">
                                {{ substr($admin->firstname, 0, 1) }}{{ substr($admin->surname, 0, 1) }}
                            </div>
                            <span>{{ $admin->surname }}, {{ $admin->firstname }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-2 text-gray-400">{{ $admin->email }}</td>
                    <td class="py-3 px-2"><x-badge :status="$admin->role" /></td>
                    <td class="py-3 px-2">
                        <span class="{{ $admin->status ? 'text-success' : 'text-danger' }} text-xs">
                            {{ $admin->status ? 'Active' : 'Inactive' }}
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
        new DataTable('#administrators-table', {
            pageLength: 25,
            order: [[0, 'asc']],
        });
    });
</script>
@endpush