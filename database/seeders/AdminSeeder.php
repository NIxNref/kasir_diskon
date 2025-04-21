<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@arkas.my.id',
            'password' => Hash::make('password'),
            'role' => 'admin', // Sesuai dengan ENUM yang didefinisikan
            'isDeleted' => 0,
        ]);

        User::create([
            'name' => 'test',
            'email' => 'test@arkas.my.id',
            'password' => Hash::make('password'),
            'role' => 'kasir', // Sesuai dengan ENUM yang didefinisikan
            'isDeleted' => 0,
        ]);
    }
}
