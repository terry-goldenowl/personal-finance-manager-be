<?php

namespace App\Services;

use App\Models\User;

class UserServices
{
    public function create(array $data): ?User
    {
        $newUser = User::create($data);
        return $newUser;
    }

    public function getUserByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();
        return $user;
    }

    public function getUser(int $id): ?User
    {
        $user = User::find($id);
        return $user;
    }

    public function getUsers()
    {
        $users = User::all();
        return $users;
    }

    public function updateUser($data, int $id): bool
    {
        return User::find($id)->update($data);
    }

    public function checkExistsByEmail(string $email)
    {
        return User::where('email', $email)->exists();
    }

    public function checkExistsByEmailAndCode(string $email, string $code)
    {
        return User::where(['email' => $email, 'verification_code' => $code])->first();
    }
}
