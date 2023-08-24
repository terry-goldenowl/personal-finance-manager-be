<?php

namespace App\Services;

use App\Models\CategoryPlan;
use App\Models\MonthPlan;

class PlanServices
{
    public function createMonthPlan(array $data): ?MonthPlan
    {
        if (MonthPlan::where([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year']
        ])->exists()) {
            return null;
        }

        $newMonthPlan = MonthPlan::create($data);
        return $newMonthPlan;
    }

    public function createCategoryPlan(array $data): ?CategoryPlan
    {
        if (CategoryPlan::where([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year'],
            'category_id' => $data['category_id']
        ])->exists()) {
            return null;
        }

        $newCategoryPlan = CategoryPlan::create($data);
        return $newCategoryPlan;
    }

    public function updateMonthPlan($data, int $id): bool
    {
        $plan = MonthPlan::find($id);
        if (!$plan) {
            return false;
        }

        if (MonthPlan::where([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year'],
        ])->where('id', '!=', $id)->exists()) {
            return false;
        }

        return $plan->update($data);
    }

    public function updateCategoryPlan($data, int $id): bool
    {
        $plan = CategoryPlan::find($id);
        if (!$plan) {
            return false;
        }

        if (CategoryPlan::where([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year'],
            'category_id' => $data['category_id']
        ])->where('id', '!=', $id)->exists()) {
            return false;
        }

        return $plan->update($data);
    }

    public function deleteMonthPlan($id): bool
    {
        $plan = MonthPlan::find($id);
        if (!$plan) {
            return false;
        }

        return MonthPlan::destroy($id);
    }

    public function deleteCategoryPlan($id): bool
    {
        $plan = CategoryPlan::find($id);
        if (!$plan) {
            return false;
        }

        return CategoryPlan::destroy($id);
    }
}
