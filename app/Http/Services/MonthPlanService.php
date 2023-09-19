<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Models\MonthPlan;
use App\Models\User;
use Exception;

class MonthPlanService extends BaseService
{
    protected $walletService;

    public function __construct(WalletServices $walletService)
    {
        parent::__construct(MonthPlan::class);
        $this->walletService = $walletService;
    }

    public function create(User $user, array $data): array
    {
        try {

            if (!$this->walletService->checkExistsById($data['wallet_id']))
                return ReturnType::fail('Wallet not found!');

            $planData = array_merge($data, ['user_id' => $user->id]);

            if ($this->checkExists($planData)) {
                return ReturnType::fail('Plan is already exists!');
            }

            $newPlan = $this->model::create($planData);

            if (!$newPlan) {
                return ReturnType::fail('This plan has been set before by this user!');
            }

            return ReturnType::success('Create month plan successfully!', ['plan' => $newPlan]);
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

            $plans = $user->month_plans()->where('year', $year)->where('wallet_id', $walletId);

            if ($month) {
                $plans->where('month', $month);
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
                return ReturnType::fail("Month plan not found!");
            }

            $planData = array_merge($data, ['user_id' => $user->id]);

            $updated = $plan->update($planData);

            if (!$updated) {
                return ReturnType::fail('Plan is not created!');
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
        return $this->model::where([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year'],
        ])->exists();
    }
}
