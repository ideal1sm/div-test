<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['title' => 'Пользователь', 'slug' => 'user'],
            ['title' => 'Менеджер', 'slug' => 'manager'],
        ];

        Role::insert($roles);
    }
}
