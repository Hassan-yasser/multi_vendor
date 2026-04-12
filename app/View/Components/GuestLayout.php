<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public function __construct(
        public string $variant = 'login',
    ) {}

    public function render(): View
    {
        return view('layouts.guest');
    }
}
