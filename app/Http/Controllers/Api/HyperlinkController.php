<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHyperlinkRequest;
use Illuminate\Http\Request;
use App\Models\Hyperlink;

class HyperlinkController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(StoreHyperlinkRequest::class, 'hyperlink');
    }

    public function index(StoreHyperlinkRequest $request)
    {
        return $request->user()
            ->hyperlinks()
            ->with('category')
            ->latest()
            ->get();
    }

    public function store(StoreHyperlinkRequest $request)
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

    public function authorizeResource(StoreHyperlinkRequest $request)
    {
        $this->authorize('create', Hyperlink::class);
    }
}
