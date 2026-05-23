@extends('layouts.admin')

@section('title', 'Administrators - TOPCIT')
@section('page-title', 'Administrators')

@section('admin-content')
@php
    $rowsJson = $administrators->getCollection()->map(function($a) {
        $initials = e(substr($a->firstname, 0, 1) . substr($a->surname, 0, 1));
        $nameHtml = '<div class="flex items-center gap-2"><div class="w-7 h-7 rounded-full bg-primary/30 flex items-center justify-center text-xs font-medium">' . $initials . '</div><span>' . e($a->surname . ', ' . $a->firstname) . '</span></div>';
        $statusBadge = $a->status
            ? '<span class="text-success text-xs font-medium">Active</span>'
            : '<span class="text-danger text-xs font-medium">Inactive</span>';
        return [
            'name' => $nameHtml,
            'email' => e($a->email),
            'role' => '<x-badge :status="$a->role" />',
            'status' => $statusBadge,
            '_actions' => '<button class="text-xs text-primary hover:text-secondary transition-colors">Edit</button>',
        ];
    });
@endphp

<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Administrators</h3>
        <button @@click="$dispatch('open-modal', 'addAdministrator')" class="btn-primary text-xs py-1.5 px-3">+ Add Admin</button>
    </div>
    <x-table :rows="$rowsJson" :perPage="25" actions :columns="[
        ['label' => 'Name', 'field' => 'name'],
        ['label' => 'Email', 'field' => 'email'],
        ['label' => 'Role', 'field' => 'role'],
        ['label' => 'Status', 'field' => 'status'],
    ]" />
</div>
@endsection