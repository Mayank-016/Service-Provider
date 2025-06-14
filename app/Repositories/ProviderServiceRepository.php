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
}
