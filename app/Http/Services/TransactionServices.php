<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Helpers\StorageHelper;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TransactionServices extends BaseService
{
    protected $categoryService;
    protected $walletService;

    public function __construct(CategoryServices $categoryService, WalletServices $walletService)
    {
        parent::__construct(Transaction::class);
        $this->categoryService = $categoryService;
        $this->walletService = $walletService;
    }

    public function create(User $user, array $data): array
    {
        try {
            if (!$this->categoryService->checkExistsById($data['category_id']))
                return ReturnType::fail('Category not found!');

            if (!$this->walletService->checkExistsById($data['wallet_id']))
                return ReturnType::fail('Wallet not found!');

            $transactionData = array_merge($data, ['user_id' => $user->id]);

            // CREATE CATEGORY FOR USER IF CATEGORY IS DEFAULT
            $category = $this->categoryService->getById($data['category_id']);

            if ($category && $category->default == true) {
                $newCategory = $this->categoryService->createCategory([
                    'name' => $category->name,
                    'type' => $category->type,
                    'image' => $category->image,
                    'user_id' => $user->id,
                    'default' => false
                ]);
            }

            if (isset($newCategory)) {
                $transactionData = array_merge($transactionData, ['category_id' => $newCategory->id]);
            }

            // STORE AND RETREIVE IMAGE
            $image = isset($data['image']) ? $data['image'] : null;
            if ($image) {
                $imageUrl = StorageHelper::store($image, "/public/images/transactions");
                $transactionData = array_merge($data, ['image' => $imageUrl]);
            }

            $newTransaction = $this->model::create($transactionData);

            if (!$newTransaction) {
                return ReturnType::fail('Create transaction failed!');
            }

            return ReturnType::success('Create transaction successfully!', ['transaction' => $newTransaction]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(User $user, array $inputs): array
    {
        try {
            $day = isset($inputs['day']) ? $inputs["day"] : null;
            $month = isset($inputs['month']) ? $inputs["month"] : null;
            $year = isset($inputs['year']) ? $inputs["year"] : null;
            $wallet = isset($inputs['wallet']) ? $inputs["wallet"] : null;
            $category = isset($inputs['category']) ? $inputs["category"] : null;
            $transactionType = isset($inputs['transaction_type']) ? $inputs["transaction_type"] : 'total';
            $search = isset($inputs['search']) ? $inputs["search"] : "";
            $page = isset($inputs['page']) ? $inputs["page"] : null;

            $query = $user->transactions();

            if ($category) {
                $query->where('category_id', $category);
            }

            if ($transactionType != 'total') {
                $query->whereHas('category', function (Builder $query) use ($transactionType) {
                    $query->where('type', $transactionType);
                });
            }

            if ($wallet) {
                $query->where('wallet_id', $wallet);
            }

            if ($day) {
                $query->whereDay('date', $day);
            }

            if ($month) {
                $query->whereMonth('date', $month);
            }

            if ($year) {
                $query->whereYear('date', $year);
            }

            if (strlen($search) > 0) {
                $query->where('title', 'LIKE', '%' . ($search) . '%')->orWhere('description', 'LIKE', '%' . $search . '%');
            }

            if ($page) {
                $transactionsPerPage = 10;
                $query->skip($transactionsPerPage * ($page - 1))->take($transactionsPerPage);
            }

            $transactions = $query->orderBy('date', 'desc')->with('category')->get();

            return ReturnType::success("", ['transactions' => $transactions]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function update(User $user, array $data, int $id): array
    {
        try {
            $transaction = $this->getById($id);
            if (!$transaction) {
                return ReturnType::fail('Transaction not found!');
            }

            if (isset($data['category_id'])) {
                if (!$this->categoryService->checkExistsById($data['category_id']))
                    return ReturnType::fail('Category not found!');
            }

            if (isset($data['wallet_id'])) {
                if (!$this->walletService->checkExistsById($data['wallet_id']))
                    return ReturnType::fail('Wallet not found!');
            }

            $image = isset($data['image']) ? $data['image'] : null;
            if ($image) {
                // DELETE OLD IMAGE
                if ($transaction->image) {
                    $imagePath = Str::after($transaction->image, '/storage');
                    StorageHelper::delete($imagePath);
                }

                // STORE AND RETREIVE NEW IMAGE
                $imageUrl = StorageHelper::store($image, "/public/images/transactions");
            }

            $transactionData = $image ? array_merge($data, ['user_id' => $user->id, 'image' => $imageUrl])
                : array_merge($data, ['user_id' => $user->id]);

            $updated = $transaction->update($transactionData);

            if (!$updated) {
                return ReturnType::fail('Update transaction failed!');
            }

            return ReturnType::success('Update transaction successfully!', $data);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function delete(int $id)
    {
        try {
            $transaction = $this->getById($id);
            if (!$transaction) {
                return ReturnType::fail('Transaction not found!');
            }

            if ($transaction->image) {
                $imagePath = Str::after($transaction->image, '/storage');
                StorageHelper::delete($imagePath);
            }

            $deleted = $this->model::destroy($id);
            if (!$deleted) {
                return ReturnType::fail('Can not delete transaction!');
            }

            return ReturnType::success('Delete transaction successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function getById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function deleleByCategory(int $categoryId)
    {
        Transaction::where('category_id', $categoryId)->delete();
    }
}
