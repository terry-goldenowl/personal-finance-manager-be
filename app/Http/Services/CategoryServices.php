<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Helpers\StorageHelper;
use App\Models\Category;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryServices extends BaseService
{

    public function __construct()
    {
        parent::__construct(Category::class);
    }

    public function checkExists($userId, $name): bool
    {
        if (Category::where(['user_id' => $userId, 'name' => $name])->exists()) {
            return false;
        }
        return true;
    }

    public function create(User $user, array $data): ?array
    {
        try {
            if (!in_array($data['type'], ['incomes', 'expenses'])) {
                return ReturnType::fail(['type' => "Type is invalid! Available types: [incomes, expenses]"]);
            }

            if (!$this->checkExists($user->id, $data['name'])) {
                return ReturnType::fail(['name' => "This category name has been used!"]);
            }

            $image = $data['image'];
            $imageUrl = StorageHelper::store($image, "/public/images/categories");

            $categoryData = array_merge($data, ['user_id' => $user->id, 'image' => $imageUrl]);
            $newCategory = $this->createCategory($categoryData);

            return ReturnType::success('Create category successfully!', ['category' => $newCategory]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(User $user, array $inputs): array
    {
        try {
            $type = isset($inputs['type']) ? $inputs['type'] : null;
            $default = isset($inputs['default']) ?  $inputs['default'] : null;
            $ingoreExists = isset($inputs['ignore_exists']) ?  $inputs['ignore_exists'] : null;
            $month = isset($inputs['month']) ?  $inputs['month'] : null;
            $year = isset($inputs['year']) ?  $inputs['year'] : null;

            $categories = $this->model::query();

            if ($ingoreExists && $month && $year) {
                $categories->where('user_id', $user->id)
                    ->orWhereNull('user_id')->leftJoin('category_plans', 'categories.id', '=', 'category_plans.category_id')
                    ->where('category_plans.month', $month)
                    ->where('category_plans.year', $year)
                    ->whereNull('products.category_id')->select('categories.*');
            }

            $categoriesTemp = clone $categories;
            $countNames = $categoriesTemp->where('user_id', $user->id)
                ->orWhereNull('user_id')->select('name', DB::raw('count(*) as count'))->groupBy('name')->distinct()->get()->toArray();

            $categories = $categories->where('user_id', $user->id)->orWhereNull('user_id')->get()->filter(function ($category) use ($countNames, $default) {
                if ($default) {
                    $includeDefault = !is_null($category->user_id);
                } else {
                    $includeDefault = is_null($category->user_id);
                }

                foreach ($countNames as $name) {
                    if ($name['name'] == $category->name && $name['count'] > 1 && $includeDefault) return false;
                }

                return true;
            })->values();

            if ($default == 'false') {
                $categories = $categories->filter(function ($category) {
                    return $category->user_id > 0;
                })->values();
            } elseif ($default == 'true') {
                $categories = $categories->filter(function ($category) {
                    return is_null($category->user_id);
                })->values();
            }

            if ($type) {
                $categories = $categories->filter(function ($category) use ($type) {
                    return $category->type == $type;
                })->values();
            }

            return ReturnType::success("Get categories successfully", ['categories' => $categories]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function update($data, int $id): array
    {
        try {
            $category = $this->getById($id);
            if (!$category) {
                return ReturnType::fail('Category not found!');
            }

            $image = isset($data['image']) ? $data['image'] : null;
            if ($image) {
                // DELETE OLD IMAGE
                if ($category->image) {
                    $imagePath = Str::after($category->image, '/storage');
                    StorageHelper::delete($imagePath);
                }

                // STORE AND RETREIVE NEW IMAGE
                $imageUrl = StorageHelper::store($image, "/public/images/categories");
            }

            $data = $image ? array_merge($data, ['image' => $imageUrl]) : $data;
            $updated = $category->update($data);

            if (!!!$updated) {
                return ReturnType::fail('Update fails or category not found!');
            }

            return ReturnType::success('Update category successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function delete($id): array
    {
        try {
            $category = $this->getById($id);

            // Delete all transactions belongs to the category
            foreach ($category->transactions as $transaction) {
                if ($transaction->image) {
                    $imagePath = Str::after($transaction->image, '/storage');
                    StorageHelper::delete($imagePath);
                }

                app(TransactionServices::class)->delete($transaction->id);
            }

            if (!$category) {
                return ReturnType::fail('Category not found!');
            }

            if ($category->image) {
                $imagePath = Str::after($category->image, '/storage');
                StorageHelper::delete($imagePath);
            }

            $deleted = $this->model::destroy($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or category not found!');
            }

            return ReturnType::success('Delete category successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function checkExistsById(int $id): bool
    {
        return Category::where('id', $id)->exists();
    }

    public function getById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function getWithSameName(int $id, string $name): ?Category
    {
        return Category::where('name', $name)->where('id', '!=', $id)->first();
    }

    public function createCategory($data)
    {
        return $this->model::create($data);
    }
}
