<?php

namespace App\Interfaces;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BookingInterface extends BaseInterface
{
    public function getFutureBookingsForService($providerId, $serviceId);
    public function cancelBookings($providerId, $serviceId);
    public function hasFutureBookingsForService(int $providerId, int $serviceId): bool;
    public function isSlotAvailable(int $providerId, Carbon $date, Carbon $startTime, int $duration): bool;
    public function getUserBooking(int $userId, int $bookingId): ?Booking;

    public function getProviderFutureBookings(int $providerId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator;
    public function getProviderBookingsAll(int $providerId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator;
    public function getProviderBookingHistory(int $providerId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator;
    
    public function getUserFutureBookings(int $userId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator;
    public function getUserBookingsAll(int $userId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator;
    public function getUserBookingHistory(int $userId, int $perPage = DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator;
    public function bookingReport(int $id, bool $isProvider = false): array;

}
