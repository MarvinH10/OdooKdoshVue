<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'ADMIN']);
        $asistente = Role::create(['name' => 'ASISTENTE']);

        Permission::create(['name' => 'productos'])->syncRoles($admin,  $asistente);
        Permission::create(['name' => 'productos.ver'])->syncRoles($admin, $asistente);
        Permission::create(['name' => 'productos.crear'])->syncRoles($admin, $asistente);
        Permission::create(['name' => 'productos.eliminar'])->syncRoles($admin);
        Permission::create(['name' => 'productos.actualizar'])->syncRoles($admin, $asistente);
    }
}
