@extends('layouts.admin')

@section('title', 'Examinations - TOPCIT')
@section('page-title', 'Examinations')

@section('admin-content')
@php
    $rowsJson = $examinations->getCollection()->map(function($e) {
        $schedule = $e->examination_at ? date('M d, h:i A', strtotime($e->examination_at)) : '—';
        $manageUrl = url('/admin/examinations/manage/' . $e->id);
        $reviewersUrl = url('/admin/examinations/reviewers/' . $e->id);
        $statusClasses = ['text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400', 'text-xs px-2 py-0.5 rounded-full bg-success/20 text-success', 'text-xs px-2 py-0.5 rounded-full bg-gray-500/20 text-gray-400'];
        $statusLabels = ['Pending', 'Active', 'Finished'];
        $sIdx = min($e->statusInt ?? 0, 2);
        return [
            'id' => '<span class="font-mono text-xs">' . $e->id . '</span>',
            'title' => '<span class="font-medium">' . e($e->title) . '</span>',
            'questions' => '<span class="text-gray-400">' . ($e->question_count ?? 0) . '</span>',
            'duration' => '<span class="text-gray-400">' . e($e->duration) . ' min</span>',
            'status' => '<span class="' . $statusClasses[$sIdx] . '">' . $statusLabels[$sIdx] . '</span>',
            'schedule' => '<span class="text-xs text-gray-400">' . $schedule . '</span>',
            '_actions' => '<div class="flex gap-2"><a href="' . $manageUrl . '" class="text-xs text-primary hover:text-secondary transition-colors">Manage</a><a href="' . $reviewersUrl . '" class="text-xs text-gray-400 hover:text-white transition-colors">Reviewers</a></div>',
        ];
    });
@endphp

<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">All Examinations</h3>
        <button @@click="$dispatch('open-modal', 'addExamination')" class="btn-primary text-xs py-1.5 px-3">+ New Exam</button>
    </div>
    <x-table :rows="$rowsJson" :perPage="25" actions :columns="[
        ['label' => 'ID', 'field' => 'id'],
        ['label' => 'Title', 'field' => 'title'],
        ['label' => 'Questions', 'field' => 'questions'],
        ['label' => 'Duration', 'field' => 'duration'],
        ['label' => 'Status', 'field' => 'status'],
        ['label' => 'Schedule', 'field' => 'schedule'],
    ]" />
</div>
@endsection