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
            'token' => '735b4c65a109cf5a36d121854f4dc74aba2ca5b6',
            // 'token' => 'miss',
            'password' => Hash::make('987654321'),
        ]);
    }
}
