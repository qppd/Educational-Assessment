@extends('layouts.admin')

@section('title', 'Questions - TOPCIT')
@section('page-title', 'Questions — {{ $examination->title ?? '' }}')

@section('admin-content')
    <div class="card" data-aos="fade-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title mb-0">Questions for <b>{{ $examination->title ?? '' }}</b></h3>
            <button @@click="$dispatch('open-modal', 'addQuestion')" class="btn-primary text-xs py-1.5 px-3">+ New Question</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="questions-table">
                <thead>
                    <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                        <th class="text-left py-3 px-2">ID</th>
                        <th class="text-left py-3 px-2">Question</th>
                        <th class="text-left py-3 px-2">Type</th>
                        <th class="text-left py-3 px-2">Answer</th>
                        <th class="text-left py-3 px-2">Choice A</th>
                        <th class="text-left py-3 px-2">Choice B</th>
                        <th class="text-left py-3 px-2">Choice C</th>
                        <th class="text-left py-3 px-2">Choice D</th>
                        <th class="text-left py-3 px-2">Status</th>
                        <th class="text-left py-3 px-2">Professor</th>
                        <th class="text-left py-3 px-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="py-3 px-2 font-mono text-xs">{{ $question->id ?? $question['id'] }}</td>
                        <td class="py-3 px-2 max-w-xs truncate">{!! $question->question ?? $question['question'] !!}</td>
                        <td class="py-3 px-2 text-gray-400">{{ $question->type ?? $question['type'] }}</td>
                        <td class="py-3 px-2 text-gray-400">{{ $question->answer ?? $question['answer'] }}</td>
                        <td class="py-3 px-2 text-gray-400">{{ $question->choice_1 ?? $question['choice_1'] ?? '—' }}</td>
                        <td class="py-3 px-2 text-gray-400">{{ $question->choice_2 ?? $question['choice_2'] ?? '—' }}</td>
                        <td class="py-3 px-2 text-gray-400">{{ $question->choice_3 ?? $question['choice_3'] ?? '—' }}</td>
                        <td class="py-3 px-2 text-gray-400">{{ $question->choice_4 ?? $question['choice_4'] ?? '—' }}</td>
                        <td class="py-3 px-2">
                            @php $qStatus = $question->status ?? $question['status']; @endphp
                            <x-badge :status="$qStatus" />
                        </td>
                        <td class="py-3 px-2 text-xs text-gray-400">{{ $question->professor ?? $question['professor'] ?? '—' }}</td>
                        <td class="py-3 px-2">
                            <div class="flex gap-2">
                                <form action="{{ url('/admin/examinations/manage/approve') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $question->id ?? $question['id'] }}">
                                    <x-button variant="success" size="sm" type="submit">Approve</x-button>
                                </form>
                                <form action="{{ url('/admin/examinations/manage/reject') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $question->id ?? $question['id'] }}">
                                    <x-button variant="danger" size="sm" type="submit">Reject</x-button>
                                </form>
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
        new DataTable('#questions-table', {
            pageLength: 25,
            order: [[0, 'asc']],
        });
    });
</script>
@endpush