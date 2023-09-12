<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Models\CategoryPlan;
use App\Models\User;
use Exception;

class CategoryPlanService extends BaseService
{
    protected $categoryService;
    protected $walletService;

    public function __construct(CategoryServices $categoryService, WalletServices $walletService)
    {
        parent::__construct(CategoryPlan::class);
        $this->categoryService = $categoryService;
        $this->walletService = $walletService;
    }

    public function create(User $user, array $data): array
    {
        try {

            if (!$this->walletService->checkExistsById($data['wallet_id']))
                return ReturnType::fail('Wallet not found!');

            if (!$this->categoryService->checkExistsById($data['category_id'])) {
                return ReturnType::fail('Category not found!');
            }

            if (isset($data['category_id'])) {
                $category = $this->categoryService->getById($data['category_id']);

                if ($category->default == 1) {
                    $category = $this->categoryService->getWithSameName($category->id, $category->name);
                    $data['category_id'] = $category->id;
                }
            }

            $planData = array_merge($data, ['user_id' => $user->id]);

            if ($this->checkExists($planData)) {
                return ReturnType::fail('Plan is already exists!');
            }

            $newPlan = $this->model::create($planData);

            if (!$newPlan) {
                return ReturnType::fail('This plan has been set before by this user!');
            }

            return ReturnType::success('Create category plan successfully!', ['plan' => $newPlan]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(User $user, array $inputs)
    {
        try {
            $month = isset($inputs['month']) ? $inputs["month"] : null;
            $year = isset($inputs['year']) ? $inputs["year"] : null;
            $walletId = isset($inputs['wallet_id']) ? $inputs["wallet_id"] : null;
            $categoryId = isset($inputs['category_id']) ? $inputs["category_id"] : null;

            $plans = $user->category_plans()->where('month', $month)->where('year', $year)->where('wallet_id', $walletId)->with('category');

            if ($categoryId) {
                $category = $this->categoryService->getById($categoryId);
                if ($category->default == 1) {
                    $category = $this->categoryService->getWithSameName($categoryId, $category->name);
                }

                $plans = $plans->where('category_id', $category->id);
            }
            return ReturnType::success("Get plans successfully", ['plans' => $plans->get()]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function update(User $user, array $data, int $id): array
    {
        try {
            $plan = $this->getById($id);

            if (!$plan) {
                return ReturnType::fail("Category plan not found!");
            }

            $planData = array_merge($data, ['user_id' => $user->id]);

            $updated = $plan->update($planData);

            if (!$updated) {
                return ReturnType::fail('Something went wrong when update category plan');
            }

            return ReturnType::success('Update plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function delete($id): array
    {
        try {
            $deleted = $this->model::destroy($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or plan not found!');
            }
            return ReturnType::success('Delete plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function checkExists($data)
    {
        return CategoryPlan::where([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year'],
            'category_id' => $data['category_id']
        ])->exists();
    }
}
