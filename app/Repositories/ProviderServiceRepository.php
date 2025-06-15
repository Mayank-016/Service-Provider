<?php

namespace App\Repositories;

use App\Interfaces\ProviderServiceInterface;
use App\Models\ProviderService;

class ProviderServiceRepository extends BaseRepository implements ProviderServiceInterface
{
    /**
     * Return the model class this repository handles.
     *
     * @return string
     */
    protected function model(): string
    {
        return ProviderService::class;
    }
    public function updateOrCreateService($providerId, $serviceId, $price): void
    {
        $this->model->updateOrCreate(
            [
                'provider_id' => $providerId,
                'service_id' => $serviceId,
            ],
            [
                'price' => $price,
            ]
        );
        return;
    }


    public function removeService($providerId, $serviceId)
    {
        return $this->model->where('provider_id', $providerId)->where('service_id', $serviceId)->delete();
    }

    public function getServiceProviders($serviceId)
    {
        return $this->model
            ->where('service_id', $serviceId)
            ->with([
                'provider:id,name',
                'service:id,name,description'
            ])
            ->get()
            ->map(function ($item) {
                return [
                    'service_id' => $item->service_id,
                    'service_name' => $item->service->name,
                    'service_description' => $item->service->description,
                    'provider_id' => $item->provider_id,
                    'provider_name' => $item->provider->name,
                    'price' => $item->price,
                ];
            });
        ;
    }
    public function providerOffersService(int $providerId, int $serviceId): bool
    {
        return $this->model
            ->where('provider_id', $providerId)
            ->where('service_id', $serviceId)
            ->exists();
    }

}
