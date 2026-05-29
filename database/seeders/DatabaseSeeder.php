<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            VenueTableSeeder::class,
            SectionTableSeeder::class,
            \Database\Seeders\SeatTableSeeder::class,
        ]);
    }
}
