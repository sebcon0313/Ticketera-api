<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert(
            [
                [
                    'name' => 'Super Admin',
                    'slug' => 'super_admin',
                    'description' => 'Control total del sistema',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Admin',
                    'slug' => 'admin',
                    'description' => 'Gestion administrativa de eventos',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Authorizer',
                    'slug' => 'authorizer',
                    'description' => 'Autoriza accesos y validaciones',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Customer',
                    'slug' => 'customer',
                    'description' => 'Cliente comprador de entradas',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['slug'], // Unique identifier para UPDATE
            ['name', 'description', 'is_active', 'updated_at'] // Columns to update
        );
    }
}