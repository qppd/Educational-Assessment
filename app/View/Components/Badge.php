<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    public function __construct(
        public string|int $status = 'pending',
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.badge');
    }
}