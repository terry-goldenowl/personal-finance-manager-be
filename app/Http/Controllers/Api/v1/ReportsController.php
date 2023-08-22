<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ReportsHelper;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __construct(private ReportsHelper $reportsHelper)
    {
    }

    public function get(Request $request)
    {
        $returnData = $this->reportsHelper->get($request);
        return ReturnType::response($returnData);
    }
}
