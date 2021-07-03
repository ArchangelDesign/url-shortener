<?php

namespace App\Services;

use App\Exceptions\UserNotFound;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @param string $name
     * @return bool
     */
    public function userExistsByName(string $name): bool
    {
        try {
            $this->fetchUserByName($name);
            return true;
        } catch (UserNotFound $e) {
            return false;
        }
    }

    /**
     * @param string $name
     * @return User
     * @throws UserNotFound
     */
    public function fetchUserByName(string $name): User
    {
        $records = User::where('name', '=', $name)->get();
        if ($records->count() == 0)
            throw new UserNotFound();

        return $records->first();
    }

    /**
     * @param string $name
     * @return User
     */
    public function createUser(string $name): User
    {
        $token = Uuid::uuid4();
        return User::create([
            'name' => $name,
            'token' => $token
        ]);
    }

    /**
     * @param string $name
     * @throws UserNotFound
     */
    public function deleteUserByName(string $name): void
    {
        $user = $this->fetchUserByName($name);
        $user->delete();
    }

    /**
     * @param string $name
     * @return User
     * @throws UserNotFound
     */
    public function regenerateToken(string $name): User
    {
        /** @var User $user */
        $user = $this->fetchUserByName($name);
        $user->token = Uuid::uuid4();
        $user->save();

        return $user;
    }

    public function fetchUserByToken(string $token): User
    {
        $res = User::find($token);
        return $res;
    }
}
