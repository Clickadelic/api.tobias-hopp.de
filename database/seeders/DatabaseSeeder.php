<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Batman
        $admin = User::updateOrCreate(
            ['email' => 'batman@clickadelic.de'],
            [
                'id' => Str::uuid(),
                'name' => 'Batman',
                'email_verified_at' => now(),
                'password' => 'forello204$',
                'remember_token' => Str::random(10),
            ]
        );

        $admin->assignRole('admin');
    }
}
