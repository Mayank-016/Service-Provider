<?php

namespace App\Repositories;

use App\Interfaces\ServiceInterface;
use App\Models\Service;

class ServiceRepository extends BaseRepository implements ServiceInterface
{
    /**
     * Return the model class this repository handles.
     *
     * @return string
     */
    protected function model(): string
    {
        return Service::class;
    }
}
