<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CategoryInterface extends BaseInterface
{
    public function allWithService(): ?Collection;
}
