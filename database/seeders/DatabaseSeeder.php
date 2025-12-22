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
        // User automatisch erstellen
        User::create([
            'id' => Str::uuid(),
            'name' => 'Clickadelic',
            'email' => 'click@clickadelic.de',
            'email_verified_at' => now(),
            'password' => Hash::make("forell0204$"), // temporäres Passwort
            'remember_token' => Str::random(10),
        ]);
    }
}
