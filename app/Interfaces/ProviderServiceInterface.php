<?php

namespace App\Interfaces;

use App\Models\ProviderService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ProviderServiceInterface extends BaseInterface
{
    public function updateOrCreateService($providerId, $serviceId, $price): void;
    public function removeService($providerId, $serviceId);
    public function getServiceProviders($serviceId);
    public function providerOffersService(int $providerId, int $serviceId): bool;
}
