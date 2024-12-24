<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => env('USER1_NAME'),
            'odoo_uid' => env('USER1_ODOO_UID'),
            'email' => env('USER1_EMAIL'),
            'token' => env('USER1_TOKEN'),
            'password' => Hash::make(env('USER1_PASSWORD')),
        ]);

        User::create([
            'name' => env('USER2_NAME'),
            'odoo_uid' => env('USER2_ODOO_UID'),
            'email' => env('USER2_EMAIL'),
            'token' => env('USER2_TOKEN'),
            'password' => Hash::make(env('USER2_PASSWORD')),
        ]);
    }
}
