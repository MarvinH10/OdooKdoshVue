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
            'name' => 'Marvin Campos',
            'odoo_uid' => 2,
            'email' => 'marvinhectorcamposdeza@gmail.com',
            'token' => '828f81d9355df3c965fc439a5aba3c9112cf2107',
            // 'token-bdReal' => 'f5b1817e92ebe8aab90a94e1515ab2a8014e7982',
            'password' => Hash::make('987654321'),
        ]);

        User::create([
            'name' => 'Jhamil Crispin',
            'odoo_uid' => 6,
            'email' => 'j99crispin@gmail.com',
            'token' => 'b7946a934c26a7c939127b526096864e29b1dafa',
            // 'token' => 'miss',
            'password' => Hash::make('987654321'),
        ]);
    }
}
