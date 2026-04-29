<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'domainname' => 'required|string|max:255',
			'url' => 'required|url|max:255',
			'score' => 'required|numeric|min:0|max:100',
			'source' => 'nullable|string|max:255',
			'technical_contact' => 'nullable|string|max:255',
			'user_id' => 'required|exists:users,id',
		];
	}
}
