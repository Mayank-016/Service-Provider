<?php

namespace App\Repositories;

use App\Interfaces\BaseInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;

abstract class BaseRepository implements BaseInterface
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    /**
     * Get the fully qualified model class name.
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * Create model instance from container.
     *
     * @return Model
     *
     * @throws \Exception
     */
    protected function resolveModel(): Model
    {
        $model = App::make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(int|string $id, array $attributes): ?Model
    {
        $record = $this->model->find($id);
        if ($record) {
            $record->update($attributes);
        }
        return $record;
    }

    public function delete(int|string $id): bool
    {
        $record = $this->model->find($id);
        return $record ? $record->delete() : false;
    }
}
