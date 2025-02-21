<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DigitadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $digitadores = [
            'IVAN SALAZAR',
            'JOAN SACRAMENTO',
            'KAREN SALAZAR',
            'REYNALDO ENCARNACION',
            'ROSMERY FACUNDO',
        ];

        foreach ($digitadores as $digitador) {
            DB::table('digitadors')->insert([
                'name' => $digitador,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
