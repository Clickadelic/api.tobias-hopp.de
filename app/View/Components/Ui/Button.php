<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
	public string $variant;
	public string $size;
	public string $type;

	public function __construct(
		string $variant = 'default',
		string $size = 'default',
		string $type = 'button',
	) {
		$this->variant = $variant;
		$this->size = $size;
		$this->type = $type;
	}

	public function baseClasses(): string
	{
		return implode(' ', [
			'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-sm font-medium',
			'transition-colors',
			'focus:outline-none focus:ring-2 focus:ring-offset-2',
			'disabled:pointer-events-none disabled:opacity-50',
			'[&_svg]:pointer-events-none',
			'[&_svg]:size-4',
			'[&_svg]:shrink-0',
			'hover:cursor-pointer shadow-sm',
		]);
	}

	public function variantClasses(): string
	{
		return match ($this->variant) {
			'primary' => 'text-white bg-sky-600 hover:bg-sky-700 focus:ring-sky-600',
			'secondary' => 'text-white bg-slate-700 hover:bg-slate-800 focus:ring-slate-600',
			'danger' => 'text-white bg-rose-600 hover:bg-rose-700 focus:ring-rose-600',
			'warning' => 'text-white bg-amber-600 hover:bg-amber-700 focus:ring-amber-600',
			'success' => 'text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-600',
			'outline' => 'bg-transparent hover:bg-neutral-700 border border-neutral-500 hover:text-slate-100 focus:ring-neutral-500',
			'ghost' => 'bg-transparent hover:bg-neutral-700 border border-transparent hover:text-slate-100 focus:ring-neutral-500',
			'link' => 'bg-transparent hover:underline border border-transparent hover:text-slate-100 focus:ring-neutral-500',
			'red' => 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-600',
			'green' => 'text-white bg-green-600 hover:bg-green-700 focus:ring-green-600',
			'blue' => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-600',
			'slate' => 'text-white bg-slate-600 hover:bg-slate-700 focus:ring-slate-600',
			'stone' => 'text-white bg-stone-600 hover:bg-stone-700 focus:ring-stone-600',
			'zinq' => 'text-white bg-zinq-600 hover:bg-zinq-700 focus:ring-zinq-600',
			'fuchsia' => 'text-white bg-fuchsia-600 hover:bg-fuchsia-700 focus:ring-fuchsia-600',
			'purple' => 'text-white bg-purple-600 hover:bg-purple-700 focus:ring-purple-600',
			'indigo' => 'text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-600',
			'amber' => 'text-white bg-amber-600 hover:bg-amber-700 focus:ring-amber-600',
			'sky' => 'text-white bg-sky-600 hover:bg-sky-700 focus:ring-sky-600',
			'cyan' => 'text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-600',
			'teal' => 'text-white bg-teal-600 hover:bg-teal-700 focus:ring-teal-600',
			'rose' => 'text-white bg-rose-600 hover:bg-rose-700 focus:ring-rose-600',
			'lime' => 'text-white bg-lime-600 hover:bg-lime-700 focus:ring-lime-600',
			'default' => 'bg-neutral-700 hover:bg-neutral-800 focus:ring-neutral-500',
			default => 'bg-neutral-600 hover:bg-neutral-800 focus:ring-neutral-500',
		};
	}

	public function sizeClasses(): string
	{
		return match ($this->size) {
			'sm' => 'h-8 rounded-lg px-3 text-xs',
			'md' => 'text-base rounded-lg h-9 px-4 py-2 text-sm',
			'lg' => 'h-10 rounded-lg px-4',
			'xl' => 'h-12 rounded-lg px-6',
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
