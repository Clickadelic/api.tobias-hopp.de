<?php

namespace App\View\Components\UI;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;
    public string $size;

    public function __construct(
        string $variant = 'default',
        string $size = 'default',
    ) {
        $this->variant = $variant;
        $this->size = $size;
    }

    public function baseClasses(): string
    {
        return implode(' ', [
            'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium',
            'transition-colors',
            'focus-visible:outline-none',
            'focus-visible:ring-1 focus-visible:ring-ring',
            'disabled:pointer-events-none disabled:opacity-50',
            '[&_svg]:pointer-events-none',
            '[&_svg]:size-4',
            '[&_svg]:shrink-0',
        ]);
    }

    public function variantClasses(): string
    {
        return match ($this->variant) {
            'destructive' => 'bg-destructive text-white shadow hover:bg-destructive/90',
            'outline' => 'border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground',
            'secondary' => 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
            'ghost' => 'hover:bg-accent hover:text-accent-foreground',
            'link' => 'text-primary underline-offset-4 hover:underline',
            'flat' => 'bg-transparent hover:bg-transparent',
            default => 'bg-primary text-primary-foreground shadow hover:bg-primary/90',
        };
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'h-8 rounded-md px-3 text-xs',
            'lg' => 'h-10 rounded-md px-8',
            'icon' => 'h-9 w-9',
            default => 'h-9 px-4 py-2',
        };
    }

    public function classes(): string
    {
        return implode(' ', [
            $this->baseClasses(),
            $this->variantClasses(),
            $this->sizeClasses(),
        ]);
    }

    public function render(): View|Closure|string
    {
        return view('components.ui.button');
    }
}