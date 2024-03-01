<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
                'username' => 'admin1',
                'name' => 'Admin 1',
                'email' => 'admin1@gmail.com',
                'password' => Hash::make('admin'),
                'department_id' => 1,
                'status_id' => 1
            ],
            [
                'username' => 'dongthai',
                'name' => 'Võ Đông Thái',
                'email' => 'dongthai@gmail.com',
                'password' => Hash::make('dongthai'),
                'department_id' => 2,
                'status_id' => 2
            ]
        ];
        DB::table('users')->insert($users);
    }
}
