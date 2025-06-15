<?php

namespace App\Services;

use App\Constants\Role;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\InvalidInputException;
use App\Models\Service;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\ProviderAvailabilityRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProviderService
{

    private UserRepository $userRepository;
    private ServiceRepository $serviceRepository;
    private BookingRepository $bookingRepository;
    private ProviderServiceRepository $providerServiceRepository;
    private ProviderAvailabilityRepository $providerAvailabilityRepository;

    /**
     * AuthService constructor.
     *
     * @param  UserRepository  $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        ServiceRepository $serviceRepository,
        BookingRepository $bookingRepository,
        ProviderServiceRepository $providerServiceRepository,
        ProviderAvailabilityRepository $providerAvailabilityRepository
    ) {
        $this->userRepository = $userRepository;
        $this->serviceRepository = $serviceRepository;
        $this->bookingRepository = $bookingRepository;
        $this->providerServiceRepository = $providerServiceRepository;
        $this->providerAvailabilityRepository = $providerAvailabilityRepository;
    }
    public function addService(User $provider, $categoryId, $serviceName, $serviceDescription): ?Service
    {
        $service = $this->serviceRepository->findByNameAndCategory($serviceName, $categoryId);
        if ($service)
            return $service;
        return $this->serviceRepository->create([
            'category_id' => $categoryId,
            'name' => $serviceName,
            'description' => $serviceDescription
        ]);
    }

    public function manageServices(User $provider, $request)
    {
        $subscribe = $request->post('subscribe');
        $unsubscribe = $request->post('unsubscribe');
        $forceUnsubscribe = $request->post('force_unsubscribe');
        if (!empty($subscribe)) {
            foreach ($subscribe as $data) {
                $this->providerServiceRepository->updateOrCreateService($provider->id, $data['service_id'], $data['price']);
            }
        }
        if (!empty($unsubscribe)) {
            foreach ($unsubscribe as $data) {
                $hasFutureBookings = $this->bookingRepository
                    ->hasFutureBookingsForService($provider->id, $data['service_id']);

                if ($hasFutureBookings && $forceUnsubscribe) {
                    $this->bookingRepository->cancelBookings($provider->id, $data['service_id']);
                }

                if (!$hasFutureBookings || $forceUnsubscribe) {
                    $this->providerServiceRepository->removeService($provider->id, $data['service_id']);
                }
            }
        }
        return;
    }

    public function manageAvailability(User $provider, $availabilities)
    {
        if (!empty($availabilities)) {
            foreach ($availabilities as $availability) {
                $this->providerAvailabilityRepository->updateOrCreate(
                    $provider->id,
                    Carbon::parse($availability['date']),
                    Carbon::parse($availability['start_time']),
                    Carbon::parse($availability['end_time']),
                    $availability['slot_duration']
                );
            }
        }
        return;
    }

    public function getAvailability($providerId, $date)
    {
        $date = is_null($date) ? null : Carbon::parse($date);
        return $this->providerAvailabilityRepository->getProviderAvailability($providerId, $date);
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
            return $this->bookingRepository->update($booking->id, [
                'status' => BOOKING_STATUS_CANCELLED
            ]);
        }

        throw new InvalidInputException(
            "Cannot cancel booking that starts within the next hour.",
            Response::HTTP_BAD_REQUEST
        );
    }

}