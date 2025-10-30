<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'AdminProject2',
            'email' => 'adminproject2@gmail.com',
            'password' => Hash::make('caekt2006'),
            'role' => 'admin',
        ]);
    }
}
