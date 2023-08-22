<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use Exception;
use Illuminate\Http\Request;

class ReportsHelper
{
    public function get(Request $request)
    {
        // Query by: date, month, year, category, wallet, report type (total/income/expense)
        try {
            $month = $request->has('month') ? $request->input("month") : null;
            $year = $request->has('year') ? $request->input("year") : null;
            $wallet = $request->has('wallet') ? $request->input("wallet") : null;
            $transactionType = $request->has('transaction_type') ? $request->input("transaction_type") : 'total';
            $reportType = $request->has('report_type') ? $request->input("report_type") : 'expenses-incomes';

            $transactionTotals = [];
            $categoriesTotals = [];

            // REPORTS BY YEAR
            if ($year && !$month) {
                foreach ($request->user()->categories as $category) {
                    // FILTER TRANSACTIONS BY WALLET, YEAR
                    $transactions = $category->transactions()
                        ->where('wallet_id', $wallet)
                        ->whereYear('date', $year)
                        ->get();

                    foreach ($transactions as $transaction) {
                        $transactionDate = \DateTime::createFromFormat('Y-m-d', $transaction->date);
                        $month = $transactionDate->format('n');

                        // REPORTS BY REPORT TYPE: TOTAL EXPENSES/INCOMES
                        if ($reportType == "expenses-incomes") {
                            if (!!!isset($transactionTotals[$month][$category->type])) {
                                $transactionTotals[$month][$category->type] = 0;
                            }

                            $transactionTotals[$month][$category->type] += $transaction->amount;
                        }
                        // // REPORTS BY REPORT TYPE: TOTAL AMOUNT PER CATEGORY
                        elseif ($reportType == "categories") {
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
                foreach ($request->user()->categories as $category) {
                    // FILTER TRANSACTIONS BY WALLET, YEAR, MONTH
                    $transactions = $category->transactions()
                        ->where('wallet_id', $wallet)
                        ->whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->get();

                    foreach ($transactions as $transaction) {
                        $transactionDate = \DateTime::createFromFormat('Y-m-d', $transaction->date);
                        $day = $transactionDate->format('j');

                        if ($reportType == "expenses-incomes") {
                            if (!!!isset($transactionTotals[$day][$category->type])) {
                                $transactionTotals[$day][$category->type] = 0;
                            }

                            $transactionTotals[$day][$category->type] += $transaction->amount;
                        } elseif ($reportType == "categories") {
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
                            $filteredTotals[$item] = $total[$transactionType];
                        }
                    }

                    $categoriesTotals = $filteredTotals;
                }
            } else {
                if ($reportType == "categories") {
                    foreach ($categoriesTotals as $item => $total) {
                        $categoriesTotals[$item] = array_values($total)[0];
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
