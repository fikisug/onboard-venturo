<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesModel;

class SalesPromoHelper extends Venturo
{
   private $sales;

   public function __construct()
   {
       $this->sales = new SalesModel();
   }

   public function get(int $itemPerPage = 0, string $sort = '', $startDate, $endDate, $customerId = [], $promoId = [])
   {
       $sales = $this->sales->getSalesPromo($itemPerPage, $sort, $startDate, $endDate, $customerId, $promoId);

       return [
           'status' => true,
           'data'   => $sales
       ];
   }
}

