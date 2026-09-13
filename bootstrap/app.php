<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__ . '/../routes/web.php',
		api: __DIR__ . '/../routes/api.php',
		commands: __DIR__ . '/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware): void {
		// Register common route middleware aliases used by the application.
		// This ensures aliases like "auth" are available for uses such as 'auth:sanctum'.
		$middleware->alias([
			'auth' => Authenticate::class,
			'auth.basic' => AuthenticateWithBasicAuth::class,
			'cache.headers' => SetCacheHeaders::class,
			'can' => Authorize::class,
			'guest' => RedirectIfAuthenticated::class,
			'password.confirm' => RequirePassword::class,
			'signed' => ValidateSignature::class,
			'throttle' => ThrottleRequests::class,
			'verified' => EnsureEmailIsVerified::class,
		]);
	})
	->withExceptions(function (Exceptions $exceptions): void {
		//
	})->create();
