<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Sri Sulastriawati',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Di-hash biar aman
        ]);
    }
}
