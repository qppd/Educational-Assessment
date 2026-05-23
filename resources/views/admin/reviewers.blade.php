@extends('layouts.admin')

@section('title', 'Reviewers - TOPCIT')
@section('page-title', 'Reviewers &mdash; {{ $examination->title ?? '' }}')

@section('admin-content')
<div class="card" data-aos="fade-up">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title mb-0">Reviewers for <b>{{ $examination->title ?? '' }}</b></h3>
        <button @@click="$dispatch('open-modal', 'addReviewer')" class="btn-primary text-xs py-1.5 px-3">+ Upload Reviewer</button>
    </div>

    <div class="overflow-x-auto" x-data="tableData({{ json_encode($reviewers->items()) }}, [
        {label: 'ID', field: 'id'},
        {label: 'File Name', field: 'file'},
        {label: 'Status', field: 'status', badge: true},
        {label: 'Uploaded By', field: 'professor'},
        {label: 'Date Uploaded', field: 'created_at'},
    ], 25)">
        <div class="mb-3">
            <input type="text" x-model="search" placeholder="Search..." class="input-field w-full sm:w-64 text-sm" />
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-400 uppercase bg-white/5">
                    <template x-for="col in columns" :key="col.field">
                        <th class="text-left py-3 px-3 font-medium cursor-pointer select-none hover:text-white" @@click="sort(col.field)">
                            <span x-text="col.label"></span>
                            <span class="text-[10px]" x-text="sortField === col.field ? (sortDir === 'asc' ? '\u2191' : '\u2193') : '\u2195'"></span>
                        </th>
                    </template>
                    <th class="text-left py-3 px-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="filtered.length === 0">
                    <tr><td :colspan="columns.length + 1" class="text-center py-10 text-gray-500 text-sm">No records found.</td></tr>
                </template>
                <template x-for="(row, idx) in paged" :key="idx">
                    <tr class="border-t border-white/5 hover:bg-white/5">
                        <template x-for="col in columns" :key="col.field">
                            <td class="py-3 px-3">
                                <span x-show="!col.badge" x-text="row[col.field] || '—'" :class="col.field === 'file' ? 'text-primary hover:text-secondary cursor-pointer' : 'text-gray-400'"></span>
                                <span x-show="col.badge" x-text="badgeLabel(row[col.field])" :class="badgeClass(row[col.field])"></span>
                            </td>
                        </template>
                        <td class="py-3 px-3">
                            <div class="flex gap-2">
                                <form method="POST" action="{{ url('/admin/examinations/reviewer/approve') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" :value="row.id">
                                    <button type="submit" class="text-xs px-2 py-1 rounded" :class="badgeClass('1')">Approve</button>
                                </form>
                                <form method="POST" action="{{ url('/admin/examinations/reviewer/reject') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" :value="row.id">
                                    <button type="submit" class="text-xs px-2 py-1 rounded" :class="badgeClass('2')">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
            <span x-text="'Showing ' + showingFrom + ' to ' + showingTo + ' of ' + filtered.length + ' entries'"></span>
            <div class="flex gap-1 items-center">
                <button @@click="prevPage()" :disabled="page === 1" class="px-2 py-1 text-gray-400 hover:text-white disabled:text-gray-600" x-text="'\u00ab Prev'"></button>
                <span x-text="page + ' / ' + totalPages" class="px-2 font-medium"></span>
                <button @@click="nextPage()" :disabled="page === totalPages" class="px-2 py-1 text-gray-400 hover:text-white disabled:text-gray-600" x-text="'Next \u00bb'"></button>
            </div>
        </div>
    </div>
</div>

<script>
function tableData(rows, cols, perPage) {
    return {
        rows: rows,
        columns: cols,
        perPage: perPage || 25,
        page: 1,
        search: '',
        sortField: cols[0]?.field || 'id',
        sortDir: 'asc',
        get filtered() {
            let items = this.rows;
            if (this.search) {
                const q = this.search.toLowerCase();
                items = items.filter(r => this.columns.some(c => String(r[c.field] || '').toLowerCase().includes(q)));
            }
            if (this.sortField) {
                items = [...items].sort((a, b) => {
                    let va = String(a[this.sortField] || '').toLowerCase();
                    let vb = String(b[this.sortField] || '').toLowerCase();
                    if (va < vb) return this.sortDir === 'asc' ? -1 : 1;
                    if (va > vb) return this.sortDir === 'asc' ? 1 : -1;
                    return 0;
                });
            }
            return items;
        },
        get paged() { const s = (this.page - 1) * this.perPage; return this.filtered.slice(s, s + this.perPage); },
        get totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)); },
        get showingFrom() { return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1; },
        get showingTo() { return Math.min(this.page * this.perPage, this.filtered.length); },
        sort(f) {
            if (this.sortField === f) { this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc'; }
            else { this.sortField = f; this.sortDir = 'asc'; }
            this.page = 1;
        },
        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        badgeLabel(s) {
            const map = {0: 'Pending', 1: 'Approved', 2: 'Rejected', 'Pending': 'Pending', 'Approved': 'Approved', 'Rejected': 'Rejected'};
            return map[s] || s || 'Unknown';
        },
        badgeClass(s) {
            if (s == 1 || s === 'Approved') return 'text-xs px-2 py-0.5 rounded-full bg-success/20 text-success';
            if (s == 2 || s === 'Rejected') return 'text-xs px-2 py-0.5 rounded-full bg-danger/20 text-danger';
            return 'text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400';
        }
    };
}
</script>
@endsection