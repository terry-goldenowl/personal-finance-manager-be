<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Wallets\CreateWalletRequest;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;

class WalletsHelper
{
    public function create(CreateWalletRequest $request)
    {
        try {
            $validated = $request->safe()->only(['name', 'image', 'default']);


            if (Wallet::where(['user_id' => $request->user()->id, 'name' => $validated['name']])->exists()) {
                return ReturnType::fail('This wallet has been created before by this user!');
            }

            $newWallet = Wallet::create(array_merge($validated, ['user_id' => $request->user()->id]));

            if (Wallet::where('user_id', $request->user()->id)->count() <= 1) {
                $newWallet->default = 1;
                $newWallet->save();
            } else {
                if ($validated['default'] == true) {
                    Wallet::where('user_id', $request->user()->id)->where('id', '!=', $newWallet->id)->update(['default' => 0]);
                }
            }

            return ReturnType::success('Create wallet successfully!', ['wallet' => $newWallet]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(Request $request)
    {
        try {
            $wallets = $request->user()->wallets()->get();
            return ReturnType::success("Get wallets successfully", ['wallets' => $wallets]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function update(CreateWalletRequest $request, int $id)
    {
        try {
            $wallet = Wallet::find($id);
            if (!$wallet) {
                return ReturnType::fail('Wallet not found!');
            }

            $validated = $request->safe()->only(['name', 'image', 'default']);

            $wallet->update($validated);

            if ($validated['default'] == true) {
                Wallet::where('user_id', $request->user()->id)->where('id', '!=', $wallet->id)->update(['default' => 0]);
            }

            return ReturnType::success('Update wallet successfully!', ['wallet' => $wallet]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
    public function delete(Request $request, int $id)
    {
        try {
            $wallet = Wallet::find($id);
            if (!$wallet) {
                return ReturnType::fail('Wallet not found!');
            }

            Wallet::destroy($id);

            return ReturnType::success('Delete wallet successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}
