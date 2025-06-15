<?php

namespace App\Interfaces;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ProviderAvailabilityInterface extends BaseInterface
{
    public function updateOrCreate($providerId, $date, $startTime, $endTime, $slotDuration);
    public function getProviderAvailability(int $providerId, ?Carbon $date = null): array;
    public function isProviderAvailable(int $providerId, Carbon $date, Carbon $startTime): bool;
    public function getByDate($providerId, $date);
}

