<?php

namespace App\Services;

use App\Constants\Role;
use App\Exceptions\InvalidCredentialsException;
use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class CategoryService
{

    private ServiceRepository $serviceRepository;
    private CategoryRepository $categoryRepository;

    /**
     * AuthService constructor.
     *
     * @param  ServiceRepository  $serviceRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(ServiceRepository $serviceRepository, CategoryRepository $categoryRepository)
    {
        $this->serviceRepository = $serviceRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getAllCategoryService(): Collection
    {
        return $this->categoryRepository->allWithService();
    }

    public function addCategory($name, $description): Category
    {
        return $this->categoryRepository->create([
            'name' => $name,
            'description' => $description
        ]);
    }
}