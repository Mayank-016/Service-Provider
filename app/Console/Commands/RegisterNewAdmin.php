<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AuthService;

class RegisterNewAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:register-new-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new admin user via CLI and get an auth token';

    /**
     * The AuthService instance.
     */
    protected AuthService $authService;

    /**
     * Create a new command instance.
     *
     * @param  AuthService  $authService
     */
    public function __construct(AuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->ask('Enter admin name');
        $email = $this->ask('Enter admin email');
        $password = $this->secret('Enter admin password');

        // Validate inputs manually
        $validator = validator([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("- $error");
            }
            return Command::FAILURE;
        }

        try {
            $response = $this->authService->registerAndLoginAdmin($email, $name, $password);
            $this->info('Admin registered successfully.');
            $this->info('Token: ' . $response['token']);
        } catch (\Throwable $e) {
            $this->error('Failed to register admin: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}
