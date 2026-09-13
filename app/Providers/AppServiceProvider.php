<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Gate::policy(User::class, UserPolicy::class);
		Gate::policy(Category::class, CategoryPolicy::class);

		// Use our UUID-based token model
		Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
	}
}
