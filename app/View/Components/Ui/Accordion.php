<?php

namespace App\View\Components\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Accordion extends Component
{
    public bool $single;

    public function __construct(bool $single = false)
    {
        $this->single = $single;
    }

    public function render(): View
    {
        return view('components.ui.accordion');
    }
}
