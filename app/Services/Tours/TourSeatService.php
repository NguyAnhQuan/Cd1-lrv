<?php

namespace App\Services\Tours;

use App\Models\Booking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Tổng số người đã đặt theo tour (bỏ booking cancelled) — dùng cho "còn chỗ".
 */
final class TourSeatService
{
    /**
     * @param  iterable<int|string>  $tourIds
     * @return Collection<int|string, object{tour_id: int, booked_people: int|string}>
     */
    public function sumBookedPeopleKeyedByTourId(iterable $tourIds): Collection
    {
        $ids = Collection::make($tourIds)->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        return Booking::query()
            ->select('tour_id', DB::raw('COALESCE(SUM(number_of_people), 0) as booked_people'))
            ->whereIn('tour_id', $ids->all())
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
            })
            ->groupBy('tour_id')
            ->get()
            ->keyBy('tour_id');
    }
}
