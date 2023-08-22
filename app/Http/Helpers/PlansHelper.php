<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Plans\CreateCategoryPlanRequest;
use App\Http\Requests\Plans\CreateMonthPlanRequest;
use App\Models\Category;
use App\Models\CategoryPlan;
use App\Models\MonthPlan;
use Exception;
use Illuminate\Http\Request;

class PlansHelper
{
    public function createMonthPlan(CreateMonthPlanRequest $request)
    {
        try {
            $validated = $request->safe()->only(['month', 'year', 'amount']);

            if (MonthPlan::where(['user_id' => $request->user()->id, 'month' => $validated['month'], 'year' => $validated['year']])->exists()) {
                return ReturnType::fail('This plan has been set before by this user!');
            }

            $newPlan = MonthPlan::create(array_merge($validated, ['user_id' => $request->user()->id]));

            return ReturnType::success('Create month plan successfully!', ['plan' => $newPlan]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function createCategoryPlan(CreateCategoryPlanRequest $request)
    {
        try {
            $validated = $request->safe()->only(['month', 'year', 'amount', 'category_id']);

            if (!Category::where('id', $validated['category_id'])->exists()) {
                return ReturnType::fail('Category not found!');
            }

            if (CategoryPlan::where([
                'user_id' => $request->user()->id,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'category_id' => $validated['category_id']
            ])->exists()) {
                return ReturnType::fail('This plan has been set before by this user!');
            }

            $newPlan = CategoryPlan::create(array_merge($validated, ['user_id' => $request->user()->id]));

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
            $plan = MonthPlan::find($id);
            if (!$plan) {
                return ReturnType::fail('Plan not found!');
            }

            $validated = $request->safe()->only(['month', 'year', 'amount']);

            if (MonthPlan::where([
                'user_id' => $request->user()->id,
                'month' => $validated['month'],
                'year' => $validated['year'],
            ])->where('id', '!=', $id)->exists()) {
                return ReturnType::fail('Plan has already existed!');
            }

            $plan->update($validated);

            return ReturnType::success('Update plan successfully!', ['plan' => $plan]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function updateCategoryPlan(CreateCategoryPlanRequest $request, int $id)
    {
        try {
            $plan = CategoryPlan::find($id);
            if (!$plan) {
                return ReturnType::fail('Plan not found!');
            }

            $validated = $request->safe()->only(['month', 'year', 'amount', 'category_id']);

            if (CategoryPlan::where([
                'user_id' => $request->user()->id,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'category_id' => $validated['category_id'],
            ])->where('id', '!=', $id)->exists()) {
                return ReturnType::fail('Plan has already existed!');
            }

            $plan->update($validated);


            return ReturnType::success('Update plan successfully!', ['plan' => $plan]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function deleteMonthPlan(Request $request, int $id)
    {
        try {
            $plan = MonthPlan::find($id);
            if (!$plan) {
                return ReturnType::fail('Plan not found!');
            }

            MonthPlan::destroy($id);

            return ReturnType::success('Delete plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function deleteCategoryPlan(Request $request, int $id)
    {
        try {
            $plan = CategoryPlan::find($id);
            if (!$plan) {
                return ReturnType::fail('Plan not found!');
            }

            MonthPlan::destroy($id);

            return ReturnType::success('Delete plan successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}
