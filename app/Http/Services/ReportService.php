<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Models\User;
use Exception;

class ReportService
{
    public function get(User $user, array $inputs)
    {
        // Query by: date, month, year, category, wallet, report type (total/income/expense)
        try {
            $month = isset($inputs['month']) ? $inputs["month"] : null;
            $year = isset($inputs['year']) ? $inputs["year"] : null;
            $wallet = isset($inputs['wallet']) ? $inputs["wallet"] : null;
            $transactionType = isset($inpusts['transaction_type']) ? $inputs["transaction_type"] : 'total';
            $reportType = isset($inputs['report_type']) ? $inputs["report_type"] : 'expenses-incomes';

            // FILTER TRANSACTIONS BY USERS, YEARS
            $transactions = $user->transactions()->whereYear('date', $year);

            // FILTER TRANSACTIONS BY WALLETS
            if ($wallet) {
                $transactions->where('wallet_id', $wallet);
            }

            $transactionTotals = [];
            $categoriesTotals = [];

            // REPORTS BY YEAR
            if ($year && !$month) {
                foreach ($user->categories as $category) {
                    if ($category->transactions()->count() == 0) continue;
                    $transactionsY = clone $transactions;
                    $transacs = $transactionsY->where('category_id', $category->id)->get();

                    foreach ($transacs as $transaction) {
                        $transactionDate = \DateTime::createFromFormat('Y-m-d', $transaction->date);
                        $transactionMonth = $transactionDate->format('n');

                        // REPORTS BY REPORT TYPE: TOTAL EXPENSES/INCOMES
                        if ($reportType == "expenses-incomes") {
                            if (!!!isset($transactionTotals[$transactionMonth][$category->type])) {
                                $transactionTotals[$transactionMonth][$category->type] = 0;
                            }

                            $transactionTotals[$transactionMonth][$category->type] += $transaction->amount;
                        }
                        // // REPORTS BY REPORT TYPE: TOTAL AMOUNT PER CATEGORY
                        elseif ($reportType == "categories") {
                            $categoriesTotals[$category->name] = $category;
                            if (!isset($categoriesTotals[$category->name][$category->type])) {
                                $categoriesTotals[$category->name][$category->type] = 0;
                            }

                            $categoriesTotals[$category->name][$category->type] += $transaction->amount;
                        }
                    }
                }
            }

            // REPORTS BY MONTH
            if ($month && $year) {
                foreach ($user->categories as $category) {
                    $transactionsYM = clone $transactions;
                    // FILTER TRANSACTIONS BY MONTH
                    $transacs = $transactionsYM->where('category_id', $category->id)
                        ->whereMonth('date', $month)
                        ->get();

                    foreach ($transacs as $transaction) {
                        $transactionDate = \DateTime::createFromFormat('Y-m-d', $transaction->date);
                        $day = $transactionDate->format('j');

                        if ($reportType == "expenses-incomes") {
                            if (!!!isset($transactionTotals[$day][$category->type])) {
                                $transactionTotals[$day][$category->type] = 0;
                            }

                            $transactionTotals[$day][$category->type] += $transaction->amount;
                        } elseif ($reportType == "categories") {
                            $categoriesTotals[$category->name] = $category;
                            if (!isset($categoriesTotals[$category->name][$category->type])) {
                                $categoriesTotals[$category->name][$category->type] = 0;
                            }

                            $categoriesTotals[$category->name][$category->type] += $transaction->amount;
                        }
                    }
                }
            }

            // FILTER BY TRANSACTION TYPE: TOTAL/INCOMES/EXPENSES
            if ($transactionType != "total") {
                $filteredTotals = [];

                if ($reportType == "expenses-incomes") {
                    foreach ($transactionTotals as $item => $total) {
                        if (isset($total[$transactionType])) {
                            $filteredTotals[$item][$transactionType] = $total[$transactionType];
                        }
                    }

                    $transactionTotals = $filteredTotals;
                } elseif ($reportType == "categories") {
                    foreach ($categoriesTotals as $item => $total) {
                        if (isset($total[$transactionType])) {
                            $filteredTotals[$item] = $total;
                            $filteredTotals[$item]['amount'] = $total[$transactionType];
                        }
                    }

                    $categoriesTotals = $filteredTotals;
                }
            } else {
                if ($reportType == "categories") {
                    foreach ($categoriesTotals as $item => $total) {
                        if (isset($total['expenses'])) {
                            $categoriesTotals[$item]['amount'] = $total['expenses'];
                        } else {
                            $categoriesTotals[$item]['amount'] = $total['incomes'];
                        }
                    }
                } else {
                    foreach ($transactionTotals as $item => $total) {
                        foreach (['expenses', 'incomes'] as $type) {
                            if (!isset($transactionTotals[$item][$type])) {
                                $transactionTotals[$item][$type] = 0;
                            }
                        }
                    }
                }
            }

            if ($reportType == "expenses-incomes") {
                return ReturnType::success("", ['reports' => $transactionTotals]);
            } elseif ($reportType == "categories") {
                return ReturnType::success("", ['reports' => $categoriesTotals]);
            }
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}
