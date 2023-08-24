<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Wallets\CreateWalletRequest;
use App\Services\WalletServices;
use Exception;
use Illuminate\Http\Request;

class WalletsHelper
{
    private WalletServices $walletServices;
    public function __construct(WalletServices $services)
    {
        $this->walletServices = $services;
    }

    public function create(CreateWalletRequest $request)
    {
        try {
            $validated = $request->safe()->only(['name', 'image', 'default']);

            $walletData = array_merge($validated, ['user_id' => $request->user()->id]);
            $newWallet = $this->walletServices->create($walletData);

            if (!$newWallet) {
                return ReturnType::fail('This wallet has been created before by this user!');
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
            $validated = $request->safe()->only(['name', 'image', 'default']);

            $updated = $this->walletServices->update($validated, $id, $request->user()->id);

            if (!!!$updated) {
                return ReturnType::fail('Update fails or wallet not found!');
            }

            return ReturnType::success('Update wallet successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
    public function delete(Request $request, int $id)
    {
        try {
            $deleted = $this->walletServices->delete($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or wallet not found!');
            }

            return ReturnType::success('Delete category successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}
