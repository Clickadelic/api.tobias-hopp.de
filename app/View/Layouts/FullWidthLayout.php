<?php

namespace App\View\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FullWidthLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.app.full-width.layout');
    }
}
