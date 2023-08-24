<?php

namespace App\Services;

use App\Models\Wallet;

class WalletServices
{
    public function create(array $data): ?Wallet
    {
        if (Wallet::where(['user_id' => $data['user_id'], 'name' => $data['name']])->exists()) {
            return null;
        }

        $newWallet = Wallet::create($data);

        if (Wallet::where('user_id', $data['user_id'])->count() <= 1) {
            $newWallet->default = 1;
            $newWallet->save();
        } else {
            if ($data['default'] == true) {
                Wallet::where('user_id', $data['user_id'])->where('id', '!=', $newWallet->id)->update(['default' => 0]);
            }
        }

        return $newWallet;
    }

    public function update($data, int $id, int $userId): bool
    {
        $wallet = Wallet::find($id);
        if (!$wallet) {
            return false;
        }

        $updated = $wallet->update($data);

        if ($data['default'] == true) {
            Wallet::where('user_id', $userId)
                ->where('id', '!=', $wallet->id)
                ->update(['default' => 0]);
        }

        return $updated;
    }

    public function delete($id): bool
    {
        $wallet = Wallet::find($id);
        if (!$wallet) {
            return false;
        }

        return Wallet::destroy($id);
    }

    public function checkExistsById(int $id): bool
    {
        return Wallet::where('id', $id)->exists();
    }
}
