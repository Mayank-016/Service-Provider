<?php

namespace App\Repositories;

use App\Interfaces\BookingInterface;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingRepository extends BaseRepository implements BookingInterface
{
    /**
     * Return the model class this repository handles.
     *
     * @return string
     */
    protected function model(): string
    {
        return Booking::class;
    }
    /**
     * Returns a Providers future bookings for a particular service
     * @param mixed $providerId
     * @param mixed $serviceId
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function getFutureBookingsForService($providerId, $serviceId)
    {
        return $this->model
            ->where('provider_id', $providerId)
            ->where('service_id', $serviceId)
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->where('start_time', '>', now())
            ->with(['user', 'service'])
            ->get();
    }

    /**
     * Checks wether the provider has any bookings in future or not bookings for a particular service
     * @param int $providerId
     * @param int $serviceId
     * @return bool
     */
    public function hasFutureBookingsForService(int $providerId, int $serviceId): bool
    {
        return $this->model
            ->where('provider_id', $providerId)
            ->where('service_id', $serviceId)
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->where('start_time', '>', now())
            ->exists();
    }

    public function cancelBookings($providerId, $serviceId)
    {
        return $this->model
            ->where('provider_id', $providerId)
            ->where('service_id', $serviceId)
            ->where('start_time', '>', now())
            ->update(['status' => BOOKING_STATUS_CANCELLED]);
    }

    public function isSlotAvailable(int $providerId, Carbon $date, Carbon $startTime, int $duration): bool
    {
        $endTime = $startTime->copy()->addMinutes($duration);

        return !$this->model
            ->where('provider_id', $providerId)
            ->where('booking_date', $date->toDateString())
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            })
            ->exists();
    }

    /**
     * Returns a Booking Belonging to the user
     * @param int $userId
     * @param int $bookingId
     * @return Booking|null
     */
    public function getUserBooking(int $userId, int $bookingId): ?Booking
    {
        return $this->model->where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get all future bookings for a provider where status is not cancelled.
     */
    public function getProviderFutureBookings(int $providerId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator
    {
        return $this->model
            ->where('provider_id', $providerId)
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    /**
     * Get all bookings for a provider, regardless of status.
     */
    public function getProviderBookingsAll(int $providerId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator
    {
        return $this->model
            ->where('provider_id', $providerId)
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all past bookings for a provider, regardless of status.
     */
    public function getProviderBookingHistory(int $providerId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator
    {
        return $this->model
            ->where('provider_id', $providerId)
            ->where('start_time', '<', Carbon::now())
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all future bookings for a user where status is not cancelled.
     */
    public function getUserFutureBookings(int $userId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->paginate($perPage);
    }

    /**
     * Get all bookings for a user regardless of status.
     */
    public function getUserBookingsAll(int $userId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderByDesc('booking_date')
            ->paginate($perPage);
    }

    /**
     * Get all past bookings for a user regardless of status.
     */
    public function getUserBookingHistory(int $userId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('booking_date', '<', now()->toDateString())
            ->orderByDesc('booking_date')
            ->paginate($perPage);
    }

    public function bookingReport(int $id, bool $isProvider = false): array
    {
        $now = now();
        $startOfWeek = $now->copy()->startOfWeek();

        $query = $this->model->newQuery();

        if ($isProvider) {
            $query->where('provider_id', $id);
        } else {
            $query->where('user_id', $id);
        }

        $confirmed = $query->clone()->where('status', BOOKING_STATUS_CONFIRMED);
        $cancelled = $query->clone()->where('status', BOOKING_STATUS_CANCELLED);

        $todayCount = $query->clone()
            ->whereDate('booking_date', $now->toDateString())
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->count();

        $weekCount = $query->clone()
            ->whereBetween('booking_date', [$startOfWeek, $now])
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->count();

        $futureCount = $query->clone()
            ->where('booking_date', '>=', $now->toDateString())
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->count();

        $amountTotal = $confirmed->sum('price');
        $confirmedCount = $confirmed->count();
        $avgAmount = $confirmedCount > 0 ? round($amountTotal / $confirmedCount, 2) : 0;

        $mostService = $confirmed->select('service_id')
            ->groupBy('service_id')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        $lastBooking = $query->clone()->orderByDesc('booking_date')->first();
        $nextBooking = $query->clone()
            ->where('booking_date', '>=', $now->toDateString())
            ->where('status', '!=', BOOKING_STATUS_CANCELLED)
            ->orderBy('booking_date')->first();

        return [
            'role' => $isProvider ? 'provider' : 'user',
            'total_bookings' => $confirmedCount,
            'bookings_this_week' => $weekCount,
            'bookings_today' => $todayCount,
            'cancelled_bookings' => $cancelled->count(),
            'upcoming_bookings' => $futureCount,
            $isProvider ? 'total_earned' : 'total_spent' => $amountTotal,
            'average_' . ($isProvider ? 'earning' : 'spending') => $avgAmount,
            'most_requested_service_id' => $mostService?->service_id,
            'last_booking' => $lastBooking,
            'next_booking' => $nextBooking,
        ];
    }


}
