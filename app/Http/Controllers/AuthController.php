<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private AuthService $authService;

    /**
     * AuthController constructor.
     *
     * @param  AuthService  $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user registration and auto-login.
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->registerAndLogin(
            $request->input('email'),
            $request->input('name'),
            $request->input('password'),
            $request->boolean('is_supplier')
        );

        return response()->json([
            'success' => true,
            'status' => Response::HTTP_CREATED,
            'message' => 'User Registered Successfully!',
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    /**
     * Handle admin registration and auto-login.
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */
    public function registerAdmin(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->registerAndLoginAdmin(
            $request->input('email'),
            $request->input('name'),
            $request->input('password')
        );

        return response()->json([
            'success' => true,
            'status' => Response::HTTP_CREATED,
            'message' => 'User Registered Successfully!',
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    /**
     * Handle user login.
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        return response()->json([
            'success' => true,
            'status' => Response::HTTP_OK,
            'message' => 'User Logged In Successfully!',
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * Handle user logout and remove the token cookie.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function logout(Request $request): void
    {
        $user = $request->user();
        $this->authService->logOut($user);
        return;
    }
}