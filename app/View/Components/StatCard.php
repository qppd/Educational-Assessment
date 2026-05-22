<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCard extends Component
{
    public function __construct(
        public string $icon = '',
        public string $label = '',
        public int|string $value = 0,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.stat-card');
    }
}