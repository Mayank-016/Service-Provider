<?php

namespace App\Repositories;

use App\Interfaces\BookingInterface;
use App\Models\Booking;

class BookingRepository extends BaseRepository implements BookingInterface
{
    /**
     * Return the model class this repository handles.
     *
     * @return string
     */
    protected function model(): string
    {
        return Booking::class;
    }
}
