<?php

namespace App\Exports;

use App\Models\SalesModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportSalesCustomer implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $reports;

    public function __construct(array $sales)
    {
        $this->reports = $sales;
    }

    public function view(): View
    {
        return view('generate.excel.report-sales-customer', $this->reports);
    }
}
