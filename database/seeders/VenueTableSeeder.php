<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('venues')->upsert(
            [
                [
                    'name' => 'Centro Cultural Miguel Angel Asturias',
                    'address' => '24 Calle 3-81, Zona 1',
                    'city' => 'Guatemala City',
                    'country' => 'GT',
                    'seat_map_config' => json_encode(['rows' => 20, 'seats_per_row' => 30, 'vip_rows' => [1, 2, 3]]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Forum Majadas',
                    'address' => 'Calzada Roosevelt 24-80, Zona 11',
                    'city' => 'Guatemala City',
                    'country' => 'GT',
                    'seat_map_config' => json_encode(['rows' => 15, 'seats_per_row' => 24, 'vip_rows' => [1, 2]]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Cine Teatro Lux',
                    'address' => '6A Avenida 11-02, Zona 1',
                    'city' => 'Guatemala City',
                    'country' => 'GT',
                    'seat_map_config' => json_encode(['rows' => 18, 'seats_per_row' => 20, 'vip_rows' => [1]]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Centro de Convenciones Grand Tikal Futura',
                    'address' => 'Calzada Roosevelt 22-43, Zona 11',
                    'city' => 'Guatemala City',
                    'country' => 'GT',
                    'seat_map_config' => json_encode(['rows' => 25, 'seats_per_row' => 40, 'vip_rows' => [1, 2, 3, 4]]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Parque de la Industria',
                    'address' => '6 Avenida 2-19, Zona 9',
                    'city' => 'Guatemala City',
                    'country' => 'GT',
                    'seat_map_config' => json_encode(['rows' => 30, 'seats_per_row' => 50, 'vip_rows' => [1, 2, 3, 4, 5]]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['name'],
            ['address', 'city', 'country', 'seat_map_config', 'updated_at']
        );
    }
}