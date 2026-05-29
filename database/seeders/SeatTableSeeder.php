<?php

namespace Database\Seeders;

use App\Domain\Seat\Models\Seat;
use App\Domain\Section\Models\Section;
use Illuminate\Database\Seeder;

class SeatTableSeeder extends Seeder
{
    public function run(): void
    {
        Section::query()->get()->each(function (Section $section) {
            foreach (['A', 'B'] as $rowLabel) {
                foreach (range(1, 4) as $seatNumber) {
                    Seat::updateOrCreate(
                        [
                            'section_id' => $section->id,
                            'row_label' => $rowLabel,
                            'seat_number' => (string) $seatNumber,
                        ],
                        []
                    );
                }
            }
        });
    }
}
