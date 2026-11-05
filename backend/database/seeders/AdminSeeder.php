<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@teamjersey.local');
        $adminPassword = env('ADMIN_PASSWORD', 'admin12345');
        $adminName = env('ADMIN_NAME', 'Team Administrator');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
            ]
        );
    }
}
