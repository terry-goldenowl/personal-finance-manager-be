<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ReportsHelper;
use App\Http\Services\ReportService;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function get(Request $request)
    {
        $returnData = $this->reportService->get($request->user(), $request->all());
        return ReturnType::response($returnData);
    }
}
