<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DestinoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinos = ['ABTAO', 'SAN MARTIN', 'TINGO MARIA'];

        foreach ($destinos as $destino) {
            DB::table('destinos')->insert([
                'name' => $destino,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
