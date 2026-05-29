<?php

namespace App\Domain\Seat\Services;

use App\Domain\Seat\Models\Seat;
use App\Domain\Seat\Repositories\ISeatRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SeatService
{
    public function __construct(
        protected ISeatRepository $seatRepository
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->seatRepository->paginate($perPage);
    }

    public function listBySection(int $sectionId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->seatRepository->findBySection($sectionId, $perPage);
    }

    public function findById(int $id): Seat
    {
        $seat = $this->seatRepository->findById($id);

        if (! $seat) {
            abort(404, 'Seat not found');
        }

        return $seat;
    }

    public function create(array $data): Seat
    {
        return $this->seatRepository->create($data);
    }

    public function update(Seat $seat, array $data): Seat
    {
        return $this->seatRepository->update($seat, $data);
    }

    public function delete(Seat $seat): bool
    {
        return $this->seatRepository->delete($seat);
    }

    public function bulkGenerate(array $data): array
    {
        $sectionId = (int) $data['section_id'];
        $rows = $this->parseRows((string) $data['rows']);
        $seatsPerRow = (int) $data['seats_per_row'];

        $seatNumbers = array_map(static fn (int $n): string => (string) $n, range(1, $seatsPerRow));
        $existingKeys = $this->seatRepository
            ->getExistingSeatKeys($sectionId, $rows, $seatNumbers)
            ->all();
        $existingMap = array_fill_keys($existingKeys, true);

        $now = now();
        $toInsert = [];
        $skipped = 0;

        foreach ($rows as $rowLabel) {
            foreach ($seatNumbers as $seatNumber) {
                $key = $rowLabel . '|' . $seatNumber;

                if (isset($existingMap[$key])) {
                    $skipped++;

                    continue;
                }

                $toInsert[] = [
                    'section_id' => $sectionId,
                    'row_label' => $rowLabel,
                    'seat_number' => $seatNumber,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::transaction(function () use ($toInsert): void {
            foreach (array_chunk($toInsert, 500) as $chunk) {
                $this->seatRepository->insertMany($chunk);
            }
        });

        return [
            'section_id' => $sectionId,
            'rows' => $rows,
            'seats_per_row' => $seatsPerRow,
            'created_count' => count($toInsert),
            'skipped_count' => $skipped,
        ];
    }

    private function parseRows(string $rows): array
    {
        $parsed = array_filter(array_map(
            static fn (string $value): string => strtoupper(trim($value)),
            explode(',', $rows)
        ));

        $uniqueRows = array_values(array_unique($parsed));

        if (empty($uniqueRows)) {
            abort(422, 'Rows format is invalid. Use values like "A" or "A,B,C".');
        }

        return $uniqueRows;
    }
}
