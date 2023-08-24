<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionServices
{
    public function create(array $data): ?Transaction
    {
        $newCategory = Transaction::create($data);
        return $newCategory;
    }

    public function update($data, int $id, int $userId): bool
    {
        return true;
    }

    public function delete($id): bool
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return false;
        }

        return Transaction::destroy($id);
    }
}
