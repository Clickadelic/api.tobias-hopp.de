<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ForgotPasswordRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		$allowedUrls = explode(',', config('app.allowed_password_reset_urls', ''));
		$allowedUrls = array_map('trim', $allowedUrls);

		return [
			'email' => ['required', 'string', 'email'],
			'app_url' => ['nullable', 'url', function ($attribute, $value, $fail) use ($allowedUrls) {
				if ($value && !in_array($value, $allowedUrls)) {
					$fail("The {$attribute} is not in the list of allowed applications.");
				}
			}],
		];
	}
}
