<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@epharma.com',
            'password' => Hash::make('admin123'), // Securely hash the password
            'role' => 'admin',    // Assuming 'role' identifies user type
        ]);

       
    }
}
