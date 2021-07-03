<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a service user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(UserService $userService)
    {
        $name = $this->argument('name');
        $this->getOutput()->info('creating user ' . $name);
        if (empty($name)) {
            $this->error('Empty or no name given.');
            return 128;
        }
        if ($userService->userExistsByName($name)) {
            $this->error('User already exists.');
            return 128;
        }

        $newUser = $userService->createUser($name);
        $this->output->success('User created.');
        $this->comment('token: ' . $newUser->token);
        return 0;
    }
}
