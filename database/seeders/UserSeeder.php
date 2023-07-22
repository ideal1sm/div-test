<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
                'password' => Hash::make('user'),
                'role_id' => 1
            ],
            [
                'name' => 'Тестовый менеджер',
                'email' => 'testmanager@gmail.com',
                'password' => Hash::make('manager'),
                'role_id' => 2
            ],
        ];

        User::insert($users);
    }
}
