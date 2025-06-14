<?php

namespace App\Services;

use App\Constants\Role;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * AuthService constructor.
     *
     * @param  UserRepository  $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user, log them in, and return an API token.
     *
     * @param  string  $email
     * @param  string  $name
     * @param  string  $password
     * @param  bool  $isSupplier
     * @return array{token: string}
     */
    public function registerAndLogin(string $email, string $name, string $password, bool $isSupplier = false): array
    {
        $user = $this->userRepository->create([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'role' => $isSupplier ? Role::Provider : Role::User,
        ]);

        $token = $user->createToken(config('app.name'))->plainTextToken;

        return [
            'token' => $token,
        ];
    }

    /**
     * Attempt to log in a user with provided credentials.
     *
     * @param  string  $email
     * @param  string  $password
     * @return array{token: string}
     *
     * @throws InvalidCredentialsException
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        $this->resetLoginAttempts($email);

        $token = $user->createToken(config('app.name'))->plainTextToken;

        return [
            'token' => $token,
        ];
    }

    /**
     * Logs out the user by deleting all of their tokens.
     *
     * @param  User  $user
     * @return bool
     */
    public function logOut(User $user): bool
    {
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return true;
    }

    /**
     * Reset login attempt and lockout cache for the given email.
     *
     * @param  string  $email
     * @return void
     */
    private function resetLoginAttempts(string $email): void
    {
        $attemptsCacheKey = 'login_attempts_' . $email;
        $lockoutCacheKey = 'login_lockout_' . $email;

        cache()->forget($lockoutCacheKey);
        cache()->forget($attemptsCacheKey);
    }
}