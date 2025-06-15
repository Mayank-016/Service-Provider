<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetServiceProviderRequest;
use App\Services\ServiceManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    private ServiceManager $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    //
    public function getAllServices(): JsonResponse
    {
        $services = $this->serviceManager->getAllServices();
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $services,
        ], Response::HTTP_OK);
    }
    public function getServiceProviders(GetServiceProviderRequest $request)
    {
        $serviceId = $request->get('service_id');
        $providers = $this->serviceManager->getServiceProviders($serviceId);
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $providers,
        ], Response::HTTP_OK);
    }
}
