<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Pemilik',
            'email' => 'pemilik1234@gmail.com',
            'password' => Hash::make('1234'),
            'role' => 'pemilik',
        ]);

        User::create([
            'name' => 'Kasir',
            'email' => 'kasir1234@gmail.com',
            'password' => Hash::make('1234'),
            'role' => 'kasir',
        ]);
    }
}
