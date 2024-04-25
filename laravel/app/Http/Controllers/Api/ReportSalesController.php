<?php

namespace App\Http\Controllers\Api;

use App\Exports\ReportSalesCategory;
use App\Exports\ReportSalesCustomer;
use App\Helpers\Report\SalesCategoryHelper;
use App\Helpers\Report\SalesCustomerHelper;
use App\Helpers\Report\SalesPromoHelper;
use App\Helpers\Report\SalesTransactionHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Report\SalesPromoCollection;
use App\Http\Resources\Report\SalesTransactionCollection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportSalesController extends Controller
{
    private $salesPromo;
    private $salesTransaction;
    private $salesCategory;
    private $salesCustomer;

    public function __construct()
    {
        $this->salesPromo = new SalesPromoHelper();
        $this->salesTransaction = new SalesTransactionHelper();
        $this->salesCategory = new SalesCategoryHelper();
        $this->salesCustomer = new SalesCustomerHelper();
    }

    public function viewSalesCustomersPerDate(Request $request)
    {
        $customerId     = $request->id ?? null;
        $date       = $request->date ?? null;
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCustomer->getDetail($customerId, $date);

        if ($isExportExcel) {
            return Excel::download(new ReportSalesCustomer($sales), 'report-sales-customer.xls');
        }

        return response()->success($sales);
    }

    /**
     * Menampilkan report penjualan per customer
     *
     * @param Request $request
     * @return void
     */

    public function viewSalesCustomers(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCustomer->get($startDate, $endDate, $customerId);

        if ($isExportExcel) {
            return Excel::download(new ReportSalesCustomer($sales), 'report-sales-customer.xls');
        }

        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
    }

    /**
     * Menampilkan report penjualan per kategori
     *
     * @param Request $request
     * @return void
     */

    public function viewSalesCategories(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $categoryId = isset($request->category_id) ? explode(',', $request->category_id) : [];
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCategory->get($startDate, $endDate, $categoryId);

        if ($isExportExcel) {
            // dd($sales);
            return Excel::download(new ReportSalesCategory($sales), 'report-sales-category.xls');
        }

        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
    }


    public function viewSalesTransaction(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $menuId = isset($request->menu_id) ? explode(',', $request->menu_id) : [];

        $salesTransaction = $this->salesTransaction->get($request->per_page ?? 25, $request->sort ?? '', $startDate, $endDate, $customerId, $menuId);

        return response()->success(new SalesTransactionCollection($salesTransaction['data']));
    }

    /**
     * Menampilkan report pemakaian promo
     *
     * @param Request $request
     * @return void
     */
    public function viewSalesPromo(Request $request)
    {
        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        $customerId = isset($request->customer_id) ? explode(',', $request->customer_id) : [];
        $promoId = isset($request->promo_id) ? explode(',', $request->promo_id) : [];

        $sales = $this->salesPromo->get($request->per_page ?? 25, $request->sort ?? '', $startDate, $endDate, $customerId, $promoId);
        return response()->success(new SalesPromoCollection($sales['data']));
    }
}
