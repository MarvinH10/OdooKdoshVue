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
            'token' => 'e19bd0cc3cc0aa47bc0a43e67e0d1a11a49b8fe3',
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
