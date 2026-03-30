<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchUnsplashImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $collectionIds = [];

        // Support alias 'collections' as CSV or array
        $collections = $this->input('collections');
        if (is_string($collections)) {
            $collectionIds = array_merge($collectionIds, array_map('trim', explode(',', $collections)));
        } elseif (is_array($collections)) {
            $collectionIds = array_merge($collectionIds, $collections);
        }

        // Support 'collection_ids' as CSV or array
        $ids = $this->input('collection_ids');
        if (is_string($ids)) {
            $collectionIds = array_merge($collectionIds, array_map('trim', explode(',', $ids)));
        } elseif (is_array($ids)) {
            $collectionIds = array_merge($collectionIds, $ids);
        }

        // Support single 'collection_id'
        $single = $this->input('collection_id');
        if (is_string($single) && $single !== '') {
            $collectionIds[] = trim($single);
        }

        // Normalize: unique, remove empties
        $collectionIds = array_values(array_unique(array_filter($collectionIds, fn ($v) => is_string($v) && $v !== '')));

        $this->merge([
            'collection_ids' => $collectionIds,
            // Coerce numeric params if provided as strings
            'page' => $this->has('page') ? (int) $this->input('page') : $this->input('page'),
            'per_page' => $this->has('per_page') ? (int) $this->input('per_page') : $this->input('per_page'),
        ]);
    }

    public function rules(): array
    {
        return [
            'collection_id' => ['sometimes', 'string'],
            'collections' => ['sometimes'], // accepted but normalized; no direct validation
            'collection_ids' => ['sometimes', 'array', 'min:1'],
            'collection_ids.*' => ['required', 'string', 'distinct'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,30'],
        ];
    }
}
