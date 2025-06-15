<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookServiceRequest;
use App\Http\Requests\CancelBookingRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function bookService(BookServiceRequest $request)
    {
        $user = $request->user();
        $bookingDetails = $this->userService->bookService($user, $request);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Booking Successful!',
            'data' => $bookingDetails,
        ], Response::HTTP_OK);
    }

    public function cancelBooking(CancelBookingRequest $request)
    {
        $user = $request->user();
        $bookingId = $request->post('booking_id');
        $booking = $this->userService->cancelBooking($user, $bookingId);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Booking Cancellation Successful!',
            'data' => $booking,
        ], Response::HTTP_OK);
    }

    public function getFutureBookings(Request $request)
    {
        $user = $request->user();
        $bookings = $this->userService->getFutureBookings($user);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $bookings,
        ], Response::HTTP_OK);
    }
    public function getAllBookings(Request $request)
    {
        $user = $request->user();
        $bookings = $this->userService->getAllBookings($user);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $bookings,
        ], Response::HTTP_OK);
    }
    public function getBookingHistory(Request $request)
    {
        $user = $request->user();
        $bookings = $this->userService->getBookingHistory($user);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $bookings,
        ], Response::HTTP_OK);
    }

    public function getReporting(Request $request)
    {
        $user = $request->user();
        $reportingData = $this->userService->getReporting($user);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $reportingData,
        ], Response::HTTP_OK);
    }
}
