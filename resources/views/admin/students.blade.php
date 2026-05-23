@extends('layouts.admin')

@section('title', 'Students - TOPCIT')
@section('page-title', 'Students')

@section('admin-content')
@php
    $rowsJson = $students->getCollection()->map(function($s) {
        return [
            'student_no' => '<span class="font-mono text-xs">' . e($s->student_no) . '</span>',
            'name' => e($s->lastname . ', ' . $s->firstname),
            'contact' => e($s->contact ?? '—'),
            'email' => e($s->email ?? '—'),
            'account' => '<span class="text-xs ' . ($s->account_status === 'with account' ? 'text-success' : 'text-gray-500') . '">' . e($s->account_status) . '</span>',
            '_actions' => '<button class="text-xs text-primary hover:text-secondary transition-colors">Edit</button>',
        ];
    });
@endphp

<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Students</h3>
        <div class="flex gap-2">
            <a href="{{ url('/admin/student/export') }}" class="btn-secondary text-xs py-1.5 px-3">Export Template</a>
            <button @@click="$dispatch('open-modal', 'addStudent')" class="btn-primary text-xs py-1.5 px-3">+ Add Student</button>
        </div>
    </div>
    <x-table :rows="$rowsJson" :perPage="25" actions :columns="[
        ['label' => 'Student No', 'field' => 'student_no'],
        ['label' => 'Name', 'field' => 'name'],
        ['label' => 'Contact', 'field' => 'contact'],
        ['label' => 'Email', 'field' => 'email'],
        ['label' => 'Account', 'field' => 'account'],
    ]" />
</div>
@endsection