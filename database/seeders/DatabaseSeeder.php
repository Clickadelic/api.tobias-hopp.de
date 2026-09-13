<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
	use WithoutModelEvents;

	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{

		// Batman
		User::create([
			'id' => Str::uuid(),
			'name' => 'Batman',
			'email' => 'batman@clickadelic.de',
			'email_verified_at' => now(),
			'password' => Hash::make('forello204$'), // Korrigiert
			'remember_token' => Str::random(10),
		]);
	}
}
