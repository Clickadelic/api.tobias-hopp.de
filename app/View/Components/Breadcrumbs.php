<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    public array $items;

    public function __construct()
    {
        $segments = request()->segments();

        // Startseite
        if (empty($segments)) {
            $this->items = [];
            return;
        }

        $this->items = collect($segments)->map(function ($segment, $index) use ($segments) {
            return [
                'name' => ucfirst(str_replace('-', ' ', $segment)),
                'url' => '/' . implode('/', array_slice($segments, 0, $index + 1)),
            ];
        })->toArray();
    }

    public function render(): View|Closure|string
    {
        return view('components.breadcrumbs');
    }
}