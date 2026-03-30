<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchUnsplashImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'collection_id' => ['sometimes', 'string'],
            'collection_ids' => ['sometimes', 'array', 'min:1'],
            'collection_ids.*' => ['required', 'string', 'distinct'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,30'],
        ];
    }
}
