<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // Add this import

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@grtech.com',
            'password' => Hash::make('password'),
            'is_admin' => true
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@grtech.com',
            'password' => Hash::make('password'),
            'is_admin' => false
        ]);
    }
}