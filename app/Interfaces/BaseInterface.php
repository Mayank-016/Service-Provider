<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseInterface
{
    public function all(): Collection;

    public function find(int|string $id): ?Model;

    public function create(array $attributes): Model;

    public function update(int|string $id, array $attributes): ?Model;

    public function delete(int|string $id): bool;
}
