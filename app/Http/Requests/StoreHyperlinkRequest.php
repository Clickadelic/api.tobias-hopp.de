<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHyperlinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Auth ist bereits durch auth:sanctum abgesichert
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'url'         => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'is_public'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'url.url' => 'Die angegebene URL ist ungültig.',
        ];
    }
}
