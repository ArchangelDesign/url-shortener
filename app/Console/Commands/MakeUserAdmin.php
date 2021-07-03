<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:admin {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote user to admin level';

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
        $this->getOutput()->info('promoting user ' . $name);
        if (empty($name)) {
            $this->error('Empty or no name given.');
            return 128;
        }
        if (!$userService->userExistsByName($name)) {
            $this->error('User not found.');
            return 128;
        }
        $user = $userService->fetchUserByName($name);
        if ($user->is_admin) {
            $this->comment('User is already on admin level.');
            return 0;
        }
        if (!$this->confirm('Are you sure you want to promote ' . $user->name . ' to admin level?'))
            return 0;

        $userService->makeUserAdmin($user);
        $this->output->success('User promoted.');

        return 0;
    }
}
