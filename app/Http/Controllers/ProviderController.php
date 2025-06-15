<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookServiceRequest;
use App\Http\Requests\CancelBookingRequest;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\GetProviderAvailabilityRequest;
use App\Http\Requests\ManageAvailabilityRequest;
use App\Http\Requests\ManageServiceRequest;
use App\Services\ProviderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProviderController extends Controller
{
    private ProviderService $providerService;
    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function addService(CreateServiceRequest $request): JsonResponse
    {
        $provider = $request->user();
        $categoryId = $request->post('category_id');
        $serviceName = $request->post('name');
        $serviceDescription = $request->post('description');
        $service = $this->providerService->addService($provider, $categoryId, $serviceName, $serviceDescription);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $service,
        ], Response::HTTP_OK);
    }

    public function manageServices(ManageServiceRequest $request): JsonResponse
    {
        $provider = $request->user();
        $this->providerService->manageServices($provider, $request);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => null,
        ], Response::HTTP_OK);
    }

    public function manageAvailability(ManageAvailabilityRequest $request)
    {
        $provider = $request->user();
        $availabilities = $request->post('availabilities');
        $this->providerService->manageAvailability($provider, $availabilities);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => null,
        ], Response::HTTP_OK);
    }

    public function getProviderAvailability(GetProviderAvailabilityRequest $request)
    {
        $providerId = $request->get('provider_id');
        $date = $request->get('date');
        $resp = $this->providerService->getAvailability($providerId, $date);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $resp,
        ], Response::HTTP_OK);
    }
}
