<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ServiceInterface extends BaseInterface
{
    public function findByNameAndCategory($name, $category): ?Model;
}
