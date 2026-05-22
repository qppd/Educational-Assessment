@extends('layouts.admin')

@section('title', 'Reviewers - TOPCIT')
@section('page-title', 'Reviewers — {{ $examination->title ?? '' }}')

@section('admin-content')
    <div class="card" data-aos="fade-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title mb-0">Reviewers for <b>{{ $examination->title ?? '' }}</b></h3>
            <button @@click="$dispatch('open-modal', 'addReviewer')" class="btn-primary text-xs py-1.5 px-3">+ Upload Reviewer</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="reviewers-table">
                <thead>
                    <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                        <th class="text-left py-3 px-2">ID</th>
                        <th class="text-left py-3 px-2">File Name</th>
                        <th class="text-left py-3 px-2">Status</th>
                        <th class="text-left py-3 px-2">Uploaded By</th>
                        <th class="text-left py-3 px-2">Date Uploaded</th>
                        <th class="text-left py-3 px-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviewers as $reviewer)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="py-3 px-2 font-mono text-xs">{{ $reviewer->id ?? $reviewer['id'] }}</td>
                        <td class="py-3 px-2">
                            <a href="{{ route('download', ['file' => $reviewer->file ?? $reviewer['file']]) }}"
                               class="text-primary hover:text-secondary transition-colors text-xs">
                                📄 {{ $reviewer->file ?? $reviewer['file'] }}
                            </a>
                        </td>
                        <td class="py-3 px-2">
                            @php $rStatus = $reviewer->status ?? $reviewer['status']; @endphp
                            <x-badge :status="$rStatus" />
                        </td>
                        <td class="py-3 px-2 text-xs text-gray-400">{{ $reviewer->professor ?? $reviewer['professor'] ?? '—' }}</td>
                        <td class="py-3 px-2 text-xs text-gray-400">{{ $reviewer->created_at ?? $reviewer['created_at'] }}</td>
                        <td class="py-3 px-2">
                            <div class="flex gap-2">
                                <form action="{{ url('/admin/examinations/reviewer/approve') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $reviewer->id ?? $reviewer['id'] }}">
                                    <x-button variant="success" size="sm" type="submit">Approve</x-button>
                                </form>
                                <form action="{{ url('/admin/examinations/reviewer/reject') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $reviewer->id ?? $reviewer['id'] }}">
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
        new DataTable('#reviewers-table', {
            pageLength: 25,
            order: [[0, 'desc']],
        });
    });
</script>
@endpush