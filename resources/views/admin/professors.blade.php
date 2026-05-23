@extends('layouts.admin')

@section('title', 'Professors - TOPCIT')
@section('page-title', 'Professors')

@section('admin-content')
@php
    $rowsJson = $professors->getCollection()->map(function($p) {
        $statusBadge = $p->status
            ? '<span class="text-success text-xs font-medium">Active</span>'
            : '<span class="text-danger text-xs font-medium">Inactive</span>';
        return [
            'employee_no' => '<span class="font-mono text-xs">' . e($p->employee_no) . '</span>',
            'name' => e($p->surname . ', ' . $p->firstname),
            'email' => e($p->email),
            'contact' => e($p->contact),
            'status' => $statusBadge,
            '_actions' => '<button class="text-xs text-primary hover:text-secondary transition-colors">Edit</button>',
        ];
    });
@endphp

<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Professors</h3>
        <button @@click="$dispatch('open-modal', 'addProfessor')" class="btn-primary text-xs py-1.5 px-3">+ Add Professor</button>
    </div>
    <x-table :rows="$rowsJson" :perPage="25" actions :columns="[
        ['label' => 'Employee No', 'field' => 'employee_no'],
        ['label' => 'Name', 'field' => 'name'],
        ['label' => 'Email', 'field' => 'email'],
        ['label' => 'Contact', 'field' => 'contact'],
        ['label' => 'Status', 'field' => 'status'],
    ]" />
</div>
@endsection