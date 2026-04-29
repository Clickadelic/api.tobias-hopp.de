<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AccordionItem extends Component
{
    public string $title;
    public int|string $index;

    public function __construct(string $title, $index)
    {
        $this->title = $title;
        $this->index = $index;
    }

    public function render(): View
    {
        return view('components.ui.accordion-item');
    }
}
