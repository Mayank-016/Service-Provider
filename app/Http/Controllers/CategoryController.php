<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function getAllCategories(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $categories,
        ], Response::HTTP_OK);
    }

    public function getAllCategoryService(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategoryService();
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => null,
            'data' => $categories,
        ], Response::HTTP_OK);
    }

    public function addCategory(AddCategoryRequest $request){
        $newCategory = $this->categoryService->addCategory(
            $request->post('name'),
            $request->post('description')
        );
        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'Category created successfully!',
            'data' => $newCategory,
        ], Response::HTTP_OK);
    }
}
