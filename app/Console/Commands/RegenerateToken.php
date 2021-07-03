<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class RegenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:regen {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerates token for given user';

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
        $this->getOutput()->info('regenerating token for ' . $name);
        if (empty($name)) {
            $this->error('Empty or no name given.');
            return 128;
        }
        if (!$userService->userExistsByName($name)) {
            $this->error('User not found.');
            return 128;
        }

        $user = $userService->regenerateToken($name);
        $this->output->success('Token regenerated');
        $this->comment('New token: ' . $user->token);

        return 0;
    }
}
