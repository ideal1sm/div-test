<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Тестовый пользователь',
                'email' => 'testuser@gmail.com',
                'password' => 'user',
                'role_id' => 1
            ],
            [
                'name' => 'Тестовый менеджер',
                'email' => 'testmanager@gmail.com',
                'password' => 'manager',
                'role_id' => 2
            ],
        ];

        User::insert($users);
    }
}
