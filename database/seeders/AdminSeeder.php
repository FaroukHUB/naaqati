<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@naaqati.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'relais_id' => null, // admin central
                'actif' => true,
            ]
        );

        $admin->assignRole('super_admin');
    }
}
