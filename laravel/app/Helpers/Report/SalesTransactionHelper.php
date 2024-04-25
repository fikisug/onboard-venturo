<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesDetailModel;
use App\Models\SalesModel;

class SalesTransactionHelper extends Venturo
{
   private $salesDetail;

   public function __construct()
   {
       $this->salesDetail = new SalesDetailModel();
   }

   public function get(int $itemPerPage = 0, string $sort = '', $startDate, $endDate, $customerId = [], $menuId = [])
   {
       $salesDetail = $this->salesDetail->getSalesTransaction($itemPerPage, $sort, $startDate, $endDate, $customerId, $menuId);

       return [
           'status' => true,
           'data'   => $salesDetail
       ];
   }
}

