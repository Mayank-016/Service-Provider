<?php

namespace App\Repositories;

use App\Interfaces\ProviderAvailabilityInterface;
use App\Models\ProviderAvailability;

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
}
