<?php

namespace App\Repositories;

use App\Interfaces\CategoryInterface;
use App\Models\Category;

class CategoryRepository extends BaseRepository implements CategoryInterface
{
    /**
     * Return the model class this repository handles.
     *
     * @return string
     */
    protected function model(): string
    {
        return Category::class;
    }
}
