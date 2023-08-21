<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Transactions\CreateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionsHelper
{
    public function create(CreateTransactionRequest $request)
    {
        try {
            $validated = $request->safe()->only(['wallet_id', 'category_id', 'amount', 'description', 'image', 'date']);

            if (!!!Wallet::where('id', $request['wallet_id'])->exists()) {
                return ReturnType::fail('Wallet not found!');
            }

            if (!!!Category::where('id', $request['category_id'])->exists()) {
                return ReturnType::fail('Category not found!');
            }

            $newTransaction = Transaction::create(array_merge($validated, ['user_id' => $request->user()->id]));

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
            $search = $request->has('search') ? $request->input("search") : null;

            // ALL TRANSACTIONS OF USER
            $query = Transaction::where('user_id', $request->user()->id);

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

            // TRANSACTIONS BY SEARCH - DESCRIPTION
            if ($search) {
                $query->whereRaw('LOWER(description) LIKE ?', '%' . strtolower($search) . '%');
            }

            $transactions = $query->get();

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
            $transaction = Transaction::find($id);
            if (!$transaction) {
                return ReturnType::fail('Transaction not found!');
            }

            Transaction::destroy($id);

            return ReturnType::success('Delete transaction successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}
