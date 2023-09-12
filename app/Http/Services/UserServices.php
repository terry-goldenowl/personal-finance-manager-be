<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Helpers\StorageHelper;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserServices extends BaseService
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function create(array $data): ?User
    {
        $newUser = $this->model::create($data);
        return $newUser;
    }

    public function getUserByEmail(string $email): ?User
    {
        $user = $this->model::where('email', $email)->first();
        return $user;
    }

    public function getUser(int $id): ?User
    {
        $user = $this->model::find($id);
        return $user;
    }

    public function getUsers(array $inputs)
    {
        try {
            $users = $this->model::get();

            return ReturnType::success('Get users successfully!', ['users' => $users]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function updateUser(User $user, array $data): array
    {
        try {
            $photo = isset($data['photo']) ? $data['photo'] : null;
            if ($photo) {
                // DELETE OLD IMAGE
                if ($user->photo) {
                    $photoPath = Str::after($user->photo, '/storage');
                    StorageHelper::delete($photoPath);
                }

                // STORE AND RETREIVE NEW IMAGE
                $photoUrl = StorageHelper::store($photo, "/public/images/users");
            }

            $data = $photo ? array_merge($data, ['photo' => $photoUrl]) : $data;

            $user->update($data);

            return ReturnType::success("Update user successfully", ['user' => $this->getById($user->id)]);
        } catch (\Throwable $th) {
            return ReturnType::fail('Fail to update user!');
        }
    }

    public function checkExistsByEmail(string $email): bool
    {
        return $this->model::where('email', $email)->exists();
    }

    public function updatePassword(int $userId, array $data): array
    {
        try {
            $user = $this->getById($userId);

            if (!$user) {
                return ReturnType::fail('User not found!');
            }

            if (!Hash::check($data['password'], $user->password)) {
                return ReturnType::fail(['password' => 'Password is not correct!']);
            }

            $user->update(Hash::make($data['newPassword']));

            return ReturnType::success('Update password successfully!');
        } catch (\Throwable $th) {
            return ReturnType::fail('Fail to update password!');
        }
    }

    public function delete(int $id): array
    {
        try {
            $user = $this->getById($id);

            if (!$user) {
                return ReturnType::fail('User not found!');
            }

            $this->model::find($id)->delete();

            return ReturnType::success('Delete user successfully!');
        } catch (\Throwable $th) {
            return ReturnType::fail('Fail to delete user account!');
        }
    }
}
