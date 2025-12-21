<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Category;
use App\Models\Hyperlink;
use App\Policies\UserPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\HyperlinkPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class      => UserPolicy::class,
        Category::class  => CategoryPolicy::class,
        Hyperlink::class => HyperlinkPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
