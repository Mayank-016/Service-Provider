<?php

namespace App\Services;

use App\Constants\Role;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\ProviderServiceRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ServiceManager
{

    private ServiceRepository $serviceRepository;
    private CategoryRepository $categoryRepository;
    private ProviderServiceRepository $providerServiceRepository;

    /**
     * AuthService constructor.
     *
     * @param  ServiceRepository  $serviceRepository
     * @param CategoryRepository $categoryRepository
     * @param ProviderServiceRepository $providerServiceRepository
     */
    public function __construct(
        ServiceRepository $serviceRepository, 
        CategoryRepository $categoryRepository,
        ProviderServiceRepository $providerServiceRepository
        )
    {
        $this->serviceRepository = $serviceRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerServiceRepository = $providerServiceRepository;
    }

    public function getAllServices(): Collection
    {
        return $this->serviceRepository->all();
    }
    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getAllCategoryService(): Collection{
        return $this->categoryRepository->allWithService();
    }

    public function getServiceProviders($serviceId)
    {
        return $this->providerServiceRepository->getServiceProviders($serviceId);
    }
}