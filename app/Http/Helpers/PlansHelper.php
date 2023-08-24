<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Plans\CreateCategoryPlanRequest;
use App\Http\Requests\Plans\CreateMonthPlanRequest;
use App\Services\CategoryServices;
use App\Services\PlanServices;
use Exception;
use Illuminate\Http\Request;

class PlansHelper
{
    private PlanServices $planServices;
    private CategoryServices $categoryServices;

    public function __construct($planServices, $categoryServices)
    {
        $this->planServices = $planServices;
        $this->categoryServices = $categoryServices;
    }

    public function createMonthPlan(CreateMonthPlanRequest $request)
    {
        try {
            $validated = $request->safe()->only(['month', 'year', 'amount']);

            $planData = array_merge($validated, ['user_id' => $request->user()->id]);
            $newPlan = $this->planServices->createMonthPlan($planData);

            if (!$newPlan) {
                return ReturnType::fail('This plan has been set before by this user!');
            }

            return ReturnType::success('Create month plan successfully!', ['plan' => $newPlan]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function createCategoryPlan(CreateCategoryPlanRequest $request)
    {
        try {
            $validated = $request->safe()->only(['month', 'year', 'amount', 'category_id']);

            if (!$this->categoryServices->checkExistsById($validated['category_id'])) {
                return ReturnType::fail('Category not found!');
            }

            $planData = array_merge($validated, ['user_id' => $request->user()->id]);
            $newPlan = $this->planServices->createCategoryPlan($planData);

            if (!$newPlan) {
                return ReturnType::fail('This plan has been set before by this user!');
            }

            return ReturnType::success('Create category plan successfully!', ['plan' => $newPlan]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(Request $request)
    {
        try {
            $type = $request->has('type') ? $request->input("type") : null;
            $month = $request->has('month') ? $request->input("month") : null;
            $year = $request->has('year') ? $request->input("year") : null;

            if ($type && $type == "month") {
                $plans = $request->user()->month_plans()->get();
            } else {
                $plans = $request->user()->category_plans()->where('month', $month)->where('year', $year);
            }
            return ReturnType::success("Get plans successfully", ['plans' => $plans]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function updateMonthPlan(CreateMonthPlanRequest $request, int $id)
    {
        try {
            $validated = $request->safe()->only(['month', 'year', 'amount']);

            $planData = array_merge($validated, ['user_id' => $request->user()->id]);
            $updated = $this->planServices->updateMonthPlan($planData, $id);

            if (!$updated) {
                return ReturnType::fail('Plan not found or have been exists');
            }

            return ReturnType::success('Update plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function updateCategoryPlan(CreateCategoryPlanRequest $request, int $id)
    {
        try {
            $validated = $request->safe()->only(['month', 'year', 'amount', 'category_id']);

            $planData = array_merge($validated, ['user_id' => $request->user()->id]);
            $updated = $this->planServices->updateCategoryPlan($planData, $id);

            if (!$updated) {
                return ReturnType::fail('Plan not found or have been exists');
            }

            return ReturnType::success('Update plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function deleteMonthPlan(Request $request, int $id)
    {
        try {
            $deleted = $this->planServices->deleteMonthPlan($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or plan not found!');
            }
            return ReturnType::success('Delete plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function deleteCategoryPlan(Request $request, int $id)
    {
        try {
            $deleted = $this->planServices->deleteCategoryPlan($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or plan not found!');
            }

            return ReturnType::success('Delete plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}
