<?php

namespace App\Repositories;

use App\Interfaces\ProviderAvailabilityInterface;
use App\Models\Booking;
use App\Models\ProviderAvailability;
use Carbon\Carbon;

class ProviderAvailabilityRepository extends BaseRepository implements ProviderAvailabilityInterface
{
    /**
     * Return the model class this repository handles.
     *
     * @return string
     */
    protected function model(): string
    {
        return ProviderAvailability::class;
    }
    public function updateOrCreate($providerId, $date, $startTime, $endTime, $slotDuration): void
    {
        $availability = $this->model
            ->where('provider_id', $providerId)
            ->where('date', $date)
            ->first();

        // If availability doesn't exist, create it
        if (!$availability) {
            $this->model->create([
                'provider_id' => $providerId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'slot_duration' => $slotDuration,
            ]);
            return;
        }

        // Check for existing bookings on the date
        $bookings = Booking::where('provider_id', $providerId)
            ->where('booking_date', $date)
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->get();

        if ($bookings->isEmpty()) {
            // No bookings — allow full update
            $availability->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
                'slot_duration' => $slotDuration,
            ]);
            return;
        }

        // Bookings exist — validate they fit within the new range
        $minStart = $bookings->min('start_time');
        $maxEnd = $bookings->max('end_time');

        if (strtotime($minStart) >= strtotime($startTime) && strtotime($maxEnd) <= strtotime($endTime)) {
            // Bookings fall within the new time range — update start/end only
            $availability->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);
        }
    }

    public function getProviderAvailability(int $providerId, ?Carbon $date = null): array
    {
        $query = $this->model
            ->where('provider_id', $providerId)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date');

        if ($date) {
            $query->where('date', $date->toDateString());
        }

        $availabilities = $query->get();

        $result = [];

        foreach ($availabilities as $availability) {
            $start = Carbon::createFromFormat('H:i:s', $availability->start_time);
            $end = Carbon::createFromFormat('H:i:s', $availability->end_time);
            $duration = $availability->slot_duration;

            $slots = [];

            $slotTime = $start->copy();
            while ($slotTime->lt($end)) {
                $slotEnd = $slotTime->copy()->addMinutes($duration);
                if ($slotEnd->gt($end)) {
                    break;
                }
                $slots[] = $slotTime->format('H:i:s');
                $slotTime->addMinutes($duration);
            }

            // Fetch bookings for this date
            $bookings = Booking::where('provider_id', $providerId)
                ->where('booking_date', $availability->date)
                ->where('status', '!=', BOOKING_STATUS_CANCELLED)
                ->get();

            // Remove overlapping slots
            foreach ($bookings as $booking) {
                $bookingStart = Carbon::parse($booking->start_time);
                $bookingEnd = Carbon::parse($booking->end_time);

                $slots = array_filter($slots, function ($slot) use ($bookingStart, $bookingEnd, $duration) {
                    $slotStart = Carbon::createFromFormat('H:i:s', $slot);
                    $slotEnd = $slotStart->copy()->addMinutes($duration);
                    return $slotEnd->lte($bookingStart) || $slotStart->gte($bookingEnd);
                });
            }

            $result[] = [
                'date' => $availability->date,
                'slots' => array_values($slots),
                'slot_duration' => $duration,
            ];
        }

        return $result;
    }

    public function isProviderAvailable(int $providerId, Carbon $date, Carbon $startTime): bool
    {
        $availability = $this->model
            ->where('provider_id', $providerId)
            ->where('date', $date)
            ->first();

        if (!$availability) {
            return false;
        }

        $start = Carbon::createFromFormat('H:i:s', $availability->start_time);
        $end = Carbon::createFromFormat('H:i:s', $availability->end_time);

        return $startTime->between($start, $end->subMinutes($availability->slot_duration));
    }


    public function getByDate($providerId, $date)
    {
        return $this->model->where('provider_id', $providerId)->where('date', $date)->first();
    }
}
