<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionTableSeeder extends Seeder
{
    public function run(): void
    {
        $venues = DB::table('venues')->pluck('id', 'name');

        DB::table('sections')->upsert(
            [
                [
                    'venue_id' => $venues['Centro Cultural Miguel Angel Asturias'] ?? null,
                    'name' => 'Platea Central',
                    'code' => 'AST-PLT',
                    'map_config' => json_encode(['type' => 'platinum', 'rows' => 8, 'seats_per_row' => 18]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'venue_id' => $venues['Centro Cultural Miguel Angel Asturias'] ?? null,
                    'name' => 'Balcón Norte',
                    'code' => 'AST-BN',
                    'map_config' => json_encode(['type' => 'balcony', 'rows' => 6, 'seats_per_row' => 16]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'venue_id' => $venues['Forum Majadas'] ?? null,
                    'name' => 'VIP Central',
                    'code' => 'FMJ-VIP',
                    'map_config' => json_encode(['type' => 'vip', 'rows' => 5, 'seats_per_row' => 12]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'venue_id' => $venues['Forum Majadas'] ?? null,
                    'name' => 'General Este',
                    'code' => 'FMJ-EST',
                    'map_config' => json_encode(['type' => 'general', 'rows' => 10, 'seats_per_row' => 20]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'venue_id' => $venues['Cine Teatro Lux'] ?? null,
                    'name' => 'Orquesta',
                    'code' => 'LUX-ORQ',
                    'map_config' => json_encode(['type' => 'orchestra', 'rows' => 7, 'seats_per_row' => 14]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'venue_id' => $venues['Parque de la Industria'] ?? null,
                    'name' => 'Zona Expo',
                    'code' => 'PDI-EXP',
                    'map_config' => json_encode(['type' => 'expo', 'rows' => 12, 'seats_per_row' => 25]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['code'],
            ['venue_id', 'name', 'map_config', 'updated_at']
        );
    }
}