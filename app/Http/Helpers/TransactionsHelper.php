<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Transactions\CreateTransactionRequest;
use App\Services\CategoryServices;
use App\Services\TransactionServices;
use App\Services\WalletServices;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TransactionsHelper
{
    public function __construct(
        private TransactionServices $transactionServices,
        private CategoryServices $categoryServices,
        private WalletServices $walletServices
    ) {
    }

    public function create(CreateTransactionRequest $request)
    {
        try {
            $validated = $request->safe()->only(['title', 'wallet_id', 'category_id', 'amount', 'description', 'image', 'date']);

            if (!$this->categoryServices->checkExistsById($validated['category_id']))
                return ReturnType::fail('Category not found!');

            if (!$this->walletServices->checkExistsById($validated['wallet_id']))
                return ReturnType::fail('Wallet not found!');

            $transactionData = array_merge($validated, ['user_id' => $request->user()->id]);

            $newTransaction = $this->transactionServices->create($transactionData);

            if (!$newTransaction) {
                return ReturnType::fail('Create transaction failed!');
            }

            return ReturnType::success('Create transaction successfully!', ['transaction' => $newTransaction]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(Request $request)
    {
        // Query by: date, month, year, category, wallet, transaction type (total/income/expense)
        try {
            $day = $request->has('day') ? $request->input("day") : null;
            $month = $request->has('month') ? $request->input("month") : null;
            $year = $request->has('year') ? $request->input("year") : null;
            $wallet = $request->has('wallet') ? $request->input("wallet") : null;
            $category = $request->has('category') ? $request->input("category") : null;
            $transactionType = $request->has('transaction_type') ? $request->input("transaction_type") : 'total';
            $search = $request->has('search') ? $request->input("search") : "";

            // ALL TRANSACTIONS OF USER
            $query = $request->user()->transactions();

            // TRANSACTIONS BY CATEGORY
            if ($category) {
                $query->where('category_id', $category);
            }

            // TRANSACTIONS BY TYPE OF CATEGORIES
            if ($transactionType != 'total') {
                $query->whereHas('category', function (Builder $query) use ($transactionType) {
                    $query->where('type', $transactionType);
                });
            }

            // TRANSACTIONS BY WALLET
            if ($wallet) {
                $query->where('wallet_id', $wallet);
            }

            // TRANSACTIONS BY DAY
            if ($day) {
                $query->whereDay('date', $day);
            }

            // TRANSACTIONS BY MONTH
            if ($month) {
                $query->whereMonth('date', $month);
            }

            // TRANSACTIONS BY YEAR
            if ($year) {
                $query->whereYear('date', $year);
            }

            // TRANSACTIONS BY SEARCH - TITLE / DESCRIPTION
            if (strlen($search) > 0) {
                $query->whereRaw('LOWER(description) LIKE ?', '%' . strtolower($search) . '%')
                    ->orWhereRaw('LOWER(title) LIKE ?', '%' . strtolower($search) . '%');
                // $query->where('description', 'LIKE', '%' . $search . '%')->orWhere('title', 'LIKE', '%' . $search . '%');
            }

            $transactions = $query->with('category')->get();

            return ReturnType::success("", ['transactions' => $transactions]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function update(CreateTransactionRequest $request)
    {
    }

    public function delete(Request $request, int $id)
    {
        try {
            $deleted = $this->transactionServices->delete($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or transaction not found!');
            }

            return ReturnType::success('Delete transaction successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}
