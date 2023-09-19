<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Helpers\StorageHelper;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Str;

class WalletServices extends BaseService
{
    public function __construct()
    {
        parent::__construct(Wallet::class);
    }

    public function create(User $user, array $data): array
    {
        try {
            if (!$this->checkExists($user->id, $data['name'])) {
                return ReturnType::fail(['name' => "This category name has been used!"]);
            }

            $image = isset($data['image']) ? $data['image'] : null;
            $imageUrl = StorageHelper::store($image, "/public/images/wallets");

            $walletData = array_merge($data, ['user_id' => $user->id, 'image' => $imageUrl]);

            if ($this->checkExistsByName($walletData['user_id'], $walletData['name'])) {
                return ReturnType::fail(['name' => 'Wallet with this name already exists!']);
            }

            $newWallet = Wallet::create($walletData);

            if ($this->countByUser($walletData['user_id']) <= 1) {
                $newWallet->default = 1;
                $newWallet->save();
            } else {
                if ($data['default'] == true) {
                    $this->updateDefaultExcept($walletData['user_id'], $newWallet->id, false);
                }
            }

            return ReturnType::success('Create wallet successfully!', ['wallet' => $newWallet]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }

        return $newWallet;
    }

    public function get(User $user)
    {
        try {
            $wallets = $user->wallets()->get()->map(function ($wallet) use ($user) {
                $balance = $this->getBalance($user, $wallet->id);

                $wallet->balance = $balance;

                return $wallet;
            });

            return ReturnType::success("Get wallets successfully", ['wallets' => $wallets]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    private function getBalance(User $user, int $walletId)
    {
        $balance = $user->wallets()->where('id', $walletId)
            ->with('transactions.category')
            ->get()
            ->map(function ($wallet) {
                return $wallet->transactions->sum(function ($transaction) {
                    if ($transaction->category->type === 'incomes') {
                        return $transaction->amount;
                    } else {
                        return -$transaction->amount;
                    }
                });
            })
            ->sum();

        return $balance;
    }

    public function update($data, int $id): array
    {
        try {

            $wallet = $this->getById($id);
            if (!$wallet) {
                return ReturnType::fail('Wallet not found!');
            }

            $image = isset($data['image']) ? $data['image'] : null;
            if ($image) {
                // DELETE OLD IMAGE
                if ($wallet->image) {
                    $imagePath = Str::after($wallet->image, '/storage');
                    StorageHelper::delete($imagePath);
                }

                // STORE AND RETREIVE NEW IMAGE
                $imageUrl = StorageHelper::store($image, "/public/images/categories");
            }

            $data = $image ? array_merge($data, ['image' => $imageUrl]) : $data;

            $updated = $wallet->update($data);

            if ($data['default'] == true) {
                $this->updateDefaultExcept($data['user_id'], $data['wallet_id'], false);
            }

            if (!!!$updated) {
                return ReturnType::fail('Update fails or wallet not found!');
            }

            return ReturnType::success('Update wallet successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function delete($id): array
    {
        try {
            $deleted = $this->model::destroy($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or wallet not found!');
            }

            return ReturnType::success('Delete category successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function checkExistsByName(int $userId, string $name)
    {
        return Wallet::where(['user_id' => $userId, 'name' => $name])->exists();
    }

    public function countByUser(int $userId)
    {
        return Wallet::where('user_id', $userId)->count();
    }

    public function updateDefaultExcept(int $userId, int $walletIdExcept, bool $default)
    {
        return Wallet::where('user_id', $userId)->where('id', '!=', $walletIdExcept)->update(['default' => $default]);
    }

    public function checkExistsById(int $id): bool
    {
        return Wallet::where('id', $id)->exists();
    }

    public function checkExists($userId, $name): bool
    {
        if (Wallet::where(['user_id' => $userId, 'name' => $name])->exists()) {
            return false;
        }
        return true;
    }

    public function getById(int $id): ?Wallet
    {
        return Wallet::find($id);
    }
}
