<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'username' => 'admin',
            'surname' => 'Administrator',
            'firstname' => 'Super',
            'middlename' => '',
            'email' => 'admin@topcit.edu',
            'contact' => '09170000000',
            'password' => Hash::make('admin123'),
            'role' => 0,
            'status' => 1,
            'photo' => 'administrator.jpg',
        ]);

        // Sample Professor
        User::create([
            'username' => 'prof001',
            'surname' => 'Doe',
            'firstname' => 'John',
            'middlename' => 'M',
            'email' => 'john.doe@topcit.edu',
            'contact' => '09170000001',
            'password' => Hash::make('password'),
            'role' => 2,
            'status' => 1,
            'photo' => 'professor.jpg',
        ]);

        echo "Seeded: 1 admin, 1 professor\n";
    }
}