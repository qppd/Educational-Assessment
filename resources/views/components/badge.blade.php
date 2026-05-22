<span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full"
      @class([
          'bg-success/20 text-emerald-400' => $status === 'active' || $status === 1,
          'bg-amber-500/20 text-amber-400' => $status === 'pending' || $status === 0,
          'bg-gray-500/20 text-gray-400' => $status === 'finished' || $status === 2,
          'bg-danger/20 text-red-400' => $status === 'rejected' || $status === 2,
      ])>
    @switch($status)
        @case('active') @case(1) <span>●</span> Active @break
        @case('pending') @case(0) <span>◌</span> Pending @break
        @case('finished') @case(2) <span>●</span> Finished @break
        @case('rejected') <span>●</span> Rejected @break
        @default {{ $status }}
    @endswitch
</span>