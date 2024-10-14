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
            'token' => '900de622164912d0df0b698e182304e4f23fdc8c',
            'password' => Hash::make('987654321'),
        ]);

        User::create([
            'name' => 'Jhamil Crispin',
            'odoo_uid' => 6,
            'email' => 'j99crispin@gmail.com',
            'token' => '735b4c65a109cf5a36d121854f4dc74aba2ca5b6',
            'password' => Hash::make('987654321'),
        ]);
    }
}
