<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public function __construct(
        public string $type = 'success',
        public int $duration = 5000,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}