<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hyperlink;

class HyperlinkController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Hyperlink::class, 'hyperlink');
    }

    public function index(Request $request)
    {
        return $request->user()
            ->hyperlinks()
            ->with('category')
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'url'         => 'required|url',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_public'   => 'boolean',
        ]);

        return $request->user()
            ->hyperlinks()
            ->create($data);
    }

    // public function authorizeResource(Request $request)
    // {
    //     $this->authorize('create', Hyperlink::class);
    // }
}
