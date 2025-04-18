<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'name' => "$firstName $lastName",
            'email' => strtolower("$firstName") . '@arkas.my.id', // Format email: nama.depan.nama.belakang@arkas.my.id
            'password' => Hash::make('password'),
            'role' => $this->faker->randomElement(['user', 'admin', 'kasir']),
            'isDeleted' => 0,
        ];
    }
}
