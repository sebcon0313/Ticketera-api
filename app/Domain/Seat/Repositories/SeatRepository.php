<?php

namespace App\Domain\Seat\Repositories;

use App\Domain\Seat\Models\Seat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SeatRepository implements ISeatRepository
{
	public function paginate(int $perPage = 15): LengthAwarePaginator
	{
		return Seat::with('section')
			->latest()
			->paginate($perPage);
	}

	public function findBySection(int $idSection, int $perPage = 15): LengthAwarePaginator
	{
		return Seat::with('section')
			->where('section_id', $idSection)
			->latest()
			->paginate($perPage);
	}

	public function findById(int $id): ?Seat
	{
		return Seat::with('section')->find($id);
	}

	public function create(array $data): Seat
	{
		$seat = Seat::create($data);

		return $seat->load('section');
	}

	public function update(Seat $seat, array $data): Seat
	{
		$seat->update($data);

		return $seat->fresh(['section']);
	}

	public function delete(Seat $seat): bool
	{
		return $seat->delete();
	}

	public function getExistingSeatKeys(int $sectionId, array $rows, array $seatNumbers): Collection
	{
		return Seat::query()
			->where('section_id', $sectionId)
			->whereIn('row_label', $rows)
			->whereIn('seat_number', $seatNumbers)
			->get(['row_label', 'seat_number'])
			->map(fn (Seat $seat): string => $seat->row_label . '|' . $seat->seat_number);
	}

	public function insertMany(array $rows): bool
	{
		if (empty($rows)) {
			return true;
		}

		return Seat::query()->insert($rows);
	}

	public function findByVenueId(int $venueId): Collection
	{
		return Seat::query()
			->with('section')
			->whereHas('section', function ($query) use ($venueId): void {
				$query->where('venue_id', $venueId);
			})
			->get();
	}
}
