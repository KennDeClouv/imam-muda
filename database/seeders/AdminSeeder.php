<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Kenn',
            'email' => 'kenn@gmail.com',
            'username' => 'superadmin',
            'password' => 'superadmin',
            'role_id' => 1,
        ]);

        User::create([
            'name' => 'Ustadz Rujian',
            'email' => 'rujian@gmail.com',
            'username' => 'rujian',
            'password' => 'rujian',
            'role_id' => 2,
        ]);

        Admin::create([
            'user_id' => 2,
            'fullname' => 'Ustadz Rujian',
            'phone' => fake()->phoneNumber,
            'address' => fake()->address,
            'birthdate' => fake()->date(),
            'birthplace' => fake()->city(),
            'description' => fake()->realText(20),
        ]);

        for ($i = 1; $i <= 26; $i++) {
            Permission::create([
                'user_id' => 2,
                'feature_id' => $i
            ]);
        }
    }
}