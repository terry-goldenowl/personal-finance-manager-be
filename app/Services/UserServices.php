<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserServices
{
    public function create(array $data)
    {
        try {
            $newUser = User::create($data);
            return $newUser;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getUserByEmail(string $email)
    {
        try {
            $user = User::where('email', $email)->first();
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getUser(int $id)
    {
        try {
            $user = User::find($id);
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getUsers()
    {
        try {
            $users = User::all();
            return $users;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateUser($data, int $id)
    {
        User::find($id)->update($data);
    }
}
