<?php

namespace App\Services;

use App\Constants\Role;
use App\Events\BookingCancelled;
use App\Events\BookingConfirmed;
use App\Exceptions\InvalidInputException;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\ProviderAvailabilityRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class UserService
{

    private BookingRepository $bookingRepository;
    private ProviderServiceRepository $providerServiceRepository;
    private ProviderAvailabilityRepository $providerAvailabilityRepository;

    /**
     * AuthService constructor.
     *
     * @param  UserRepository  $userRepository
     */
    public function __construct(
        BookingRepository $bookingRepository,
        ProviderServiceRepository $providerServiceRepository,
        ProviderAvailabilityRepository $providerAvailabilityRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->providerServiceRepository = $providerServiceRepository;
        $this->providerAvailabilityRepository = $providerAvailabilityRepository;
    }

    public function bookService(User $user, $request)
    {
        $providerId = $request->post('provider_id');
        $serviceId = $request->post('service_id');
        $date = Carbon::parse($request->post('date'));
        $startTime = Carbon::parse($request->post('start_time'));

        if (!$this->providerServiceRepository->providerOffersService($providerId, $serviceId)) {
            throw new InvalidInputException('Provider does not offer this service.', Response::HTTP_BAD_REQUEST);
        }

        // Step 2: Check provider availability
        if (!$this->providerAvailabilityRepository->isProviderAvailable($providerId, $date, $startTime)) {
            throw new InvalidInputException('Provider is not available at the selected time.', Response::HTTP_BAD_REQUEST);
        }

        // Step 3: Get duration from availability
        $availability = $this->providerAvailabilityRepository->getByDate($providerId, $date);
        $duration = $availability->slot_duration;

        // Step 4: Check if slot is already booked
        if (!$this->bookingRepository->isSlotAvailable($providerId, $date, $startTime, $duration)) {
            throw new InvalidInputException('Slot is already booked.', Response::HTTP_BAD_REQUEST);
        }

        // Step 5: Create booking
        $bookingDetails = $this->bookingRepository->create([
            'user_id' => $user->id,
            'provider_id' => $providerId,
            'service_id' => $serviceId,
            'booking_date' => $date->toDateString(),
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes($duration),
            'status' => BOOKING_STATUS_CONFIRMED,
        ]);
        event(new BookingConfirmed($bookingDetails));
        return $bookingDetails;
    }

    public function cancelBooking(User $user, int $bookingId)
    {
        $booking = $this->bookingRepository->getUserBooking($user->id, $bookingId);

        if (!$booking) {
            throw new InvalidInputException("Invalid Booking ID!", Response::HTTP_BAD_REQUEST);
        }

        $startTime = Carbon::parse($booking->start_time);

        // Booking must start at least 1 hour from now to be cancelable
        if ($startTime->gt(now()->addHour())) {
            event(new BookingCancelled($booking));
            return $this->bookingRepository->update($booking->id, [
                'status' => BOOKING_STATUS_CANCELLED
            ]);
        }

        throw new InvalidInputException(
            "Cannot cancel booking that starts within the next hour.",
            Response::HTTP_BAD_REQUEST
        );
    }

    public function getFutureBookings(User $user)
    {
        if ($user->role === Role::Provider) {
            return $this->bookingRepository->getProviderFutureBookings($user->id);
        } else {
            return $this->bookingRepository->getUserFutureBookings($user->id);
        }
    }

    public function getAllBookings(User $user)
    {
        if ($user->role === Role::Provider) {
            return $this->bookingRepository->getProviderBookingsAll($user->id);
        } else {
            return $this->bookingRepository->getUserBookingsAll($user->id);
        }
    }

    public function getBookingHistory(User $user)
    {
        if ($user->role === Role::Provider) {
            return $this->bookingRepository->getProviderBookingHistory($user->id);
        } else {
            return $this->bookingRepository->getUserBookingHistory($user->id);
        }
    }

    public function getReporting(User $user)
    {
        $isProvider = $user->role === Role::Provider;
        return $this->bookingRepository->bookingReport($user->id,$isProvider);
    }

}