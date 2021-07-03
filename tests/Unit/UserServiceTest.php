<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        try {
            App::make(UserService::class)->deleteUserByName('unit-test');
        } catch (\Exception $e) {}
    }

    public function testCreateAndRemoveUser(): void
    {
        /** @var UserService $userService */
        $userService = App::make(UserService::class);
        $newUser = $userService->createUser('unit-test');
        $this->assertInstanceOf(User::class, $newUser);
        $this->assertEquals('unit-test', $newUser->name);
        $userService->deleteUserByName('unit-test');
        $this->assertFalse($userService->userExistsByName('unit-test'));
    }

    public function testTokenRegeneration(): void
    {
        /** @var UserService $userService */
        $userService = App::make(UserService::class);
        $newUser = $userService->createUser('unit-test');
        $byToken = $userService->fetchUserByToken($newUser->token);
        $this->assertEquals($newUser->token, $byToken->token);
        $userService->regenerateToken('unit-test');
        $byName = $userService->fetchUserByName('unit-test');
        $this->assertNotEquals($newUser->token, $byName->token);
        $userService->deleteUserByName('unit-test');
    }
}
