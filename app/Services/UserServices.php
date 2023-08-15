<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserServices
{
    public static function create($data)
    {
        try {
            $newUser = User::create($data);
            return $newUser;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getUserByEmail($email)
    {
        try {
            $user = User::where('email', $email)->first();
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getUser($id)
    {
        try {
            $user = User::find($id);
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getUsers()
    {
        try {
            $users = User::all();
            return $users;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
