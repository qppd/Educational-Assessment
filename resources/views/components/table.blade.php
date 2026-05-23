@props([
    'rows' => [],
    'columns' => [],
    'perPage' => 25,
    'actions' => false,
])

@php
    // If rows are Eloquent/objects, convert to plain arrays using column field access
    $rowsJson = [];
    foreach ($rows as $row) {
        $obj = [];
        foreach ($columns as $col) {
            $field = $col['field'] ?? $col[1] ?? '';
            // Check if it's already a string (pre-formatted HTML) or needs extraction
            $val = $row->$field ?? $row[$field] ?? $row[0] ?? '';
            $obj[$field] = $val;
        }
        if ($actions !== false) {
            $obj['_actions'] = is_string($actions) ? $actions : '';
        }
        $rowsJson[] = $obj;
    }
@endphp

<div
    x-data="{
        rows: {{ Js::from($rowsJson) }},
        columns: {{ Js::from($columns) }},
        perPage: {{ $perPage }},
        page: 1,
        sortField: null,
        sortDir: 'asc',
        search: '',

        get filtered() {
            let items = this.rows;
            if (this.search) {
                const q = this.search.toLowerCase();
                items = items.filter(row =>
                    this.columns.some(col => {
                        const val = row[col.field];
                        const clean = val ? String(val).replace(/<[^>]*>/g, '').toLowerCase() : '';
                        return clean.includes(q);
                    })
                );
            }
            if (this.sortField) {
                items = [...items].sort((a, b) => {
                    let va = a[this.sortField];
                    let vb = b[this.sortField];
                    const clean = (s) => String(s || '').replace(/<[^>]*>/g, '').trim();
                    va = clean(va);
                    vb = clean(vb);
                    const na = parseFloat(va);
                    const nb = parseFloat(vb);
                    if (!isNaN(na) && !isNaN(nb)) { va = na; vb = nb; }
                    if (va < vb) return this.sortDir === 'asc' ? -1 : 1;
                    if (va > vb) return this.sortDir === 'asc' ? 1 : -1;
                    return 0;
                });
            }
            return items;
        },

        get paged() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },

        get showingFrom() {
            return this.filtered.length === 0 ? 0 : (this.page - 1) * this.perPage + 1;
        },

        get showingTo() {
            return Math.min(this.page * this.perPage, this.filtered.length);
        },

        sort(field) {
            if (this.sortField === field) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDir = 'asc';
            }
            this.page = 1;
        },

        sortIcon(field) {
            if (this.sortField !== field) return '↕';
            return this.sortDir === 'asc' ? '↑' : '↓';
        },

        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },
    }"
    class="space-y-3"
>
    <!-- Search & Per-Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="relative w-full sm:w-64">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="search" placeholder="Search..."
                   class="input-field pl-10 pr-3 py-2 w-full text-sm rounded-lg bg-white/5 border border-white/10 text-gray-200 placeholder-gray-500 focus:outline-none focus:border-primary/50" />
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-400">
            <span>Show</span>
            <select x-model="perPage" @@change="page = 1"
                    class="input-field bg-white/5 border border-white/10 text-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-primary/50">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>entries</span>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-white/5">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-400 uppercase bg-white/5">
                    @foreach ($columns as $col)
                    <th class="text-left py-3 px-3 font-medium cursor-pointer select-none hover:text-white transition-colors"
                        @@click="sort('{{ $col['field'] ?? '' }}')">
                        <span class="flex items-center gap-1">
                            <span>{{ $col['label'] ?? $col['field'] ?? '' }}</span>
                            <span class="text-[10px] opacity-60" x-text="sortIcon('{{ $col['field'] ?? '' }}')"></span>
                        </span>
                    </th>
                    @endforeach
                    @if($actions)
                    <th class="text-left py-3 px-3 font-medium">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <template x-if="filtered.length === 0">
                    <tr>
                        <td :colspan="columns.length + ({{ $actions ? 'true' : 'false' }})" class="text-center py-10 text-gray-500 text-sm">
                            No records found.
                        </td>
                    </tr>
                </template>
                <template x-for="(row, rowIdx) in paged" :key="rowIdx">
                    <tr class="border-t border-white/5 hover:bg-white/5 transition-colors">
                        @foreach ($columns as $col)
                        <td class="py-3 px-3" x-html="row['{{ $col['field'] ?? '' }}']"></td>
                        @endforeach
                        @if($actions)
                        <td class="py-3 px-3" x-html="row['_actions']"></td>
                        @endif
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-gray-400">
        <span x-text="'Showing ' + showingFrom + ' to ' + showingTo + ' of ' + filtered.length + ' entries'"></span>
        <div class="flex items-center gap-1">
            <button @@click="prevPage()" :disabled="page === 1"
                    class="px-3 py-1.5 rounded-lg transition-colors"
                    :class="page === 1 ? 'text-gray-600 cursor-not-allowed' : 'text-gray-300 hover:bg-white/10 hover:text-white'">
                Previous
            </button>
            <template x-for="p in totalPages" :key="p">
                <button @@click="page = p"
                        class="px-3 py-1.5 rounded-lg transition-colors text-xs font-medium"
                        :class="page === p ? 'bg-primary/30 text-primary' : 'text-gray-400 hover:bg-white/10 hover:text-white'"
                        x-show="p === 1 || p === totalPages || Math.abs(p - page) <= 1"
                        x-text="p">
                </button>
            </template>
            <button @@click="nextPage()" :disabled="page === totalPages"
                    class="px-3 py-1.5 rounded-lg transition-colors"
                    :class="page === totalPages ? 'text-gray-600 cursor-not-allowed' : 'text-gray-300 hover:bg-white/10 hover:text-white'">
                Next
            </button>
        </div>
    </div>
</div>