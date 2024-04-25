<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Report\TotalSalesHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesSummaryController extends Controller
{
    private $sales;

    public function __construct()
    {
        $this->sales    = new TotalSalesHelper();
    }

    public function getDiagramPerYear()
    {
        $sales = $this->sales->getTotalPerYear();

        return response()->success($sales['data']);
    }

    public function getDiagramPerMonth($year)
    {
        $sale = $this->sales->getTotalPerMonths($year);
        return response()->success($sale['data']);
    }

    public function getDiagramPerCustomDate(Request $request)
    {
        $startDate = $request->startDate ?? null;
        $endDate = $request->endDate ?? null;

        $sale = $this->sales->getTotalPerCustomDate($startDate, $endDate);
        return response()->success($sale['data']);
    }

    public function getTotalSummary()
    {
        $sales = $this->sales->getTotalInPeriode();

        return response()->success($sales['data']);
    }

}
