<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

		// Clickadelic
		User::create([
			'id' => Str::uuid(),
			'name' => 'Clickadelic',
			'email' => 'click@clickadelic.de',
			'email_verified_at' => now(),
			'password' => 'forello204$',
			'remember_token' => Str::random(10),
		]);

		// Walter White
		User::create([
			'id' => Str::uuid(),
			'name' => 'WalterWhite',
			'email' => 'walter@breaking-bad.com',
			'email_verified_at' => now(),
			'password' => Hash::make('password'),
			'remember_token' => Str::random(10),
		]);

		// Jesse Pinkman
		User::create([
			'id' => Str::uuid(),
			'name' => 'JessePinkman',
			'email' => 'jesse@breaking-bad.com',
			'email_verified_at' => now(),
			'password' => Hash::make('password'),
			'remember_token' => Str::random(10),
		]);
    }
}
