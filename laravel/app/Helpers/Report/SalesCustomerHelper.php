<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesModel;
use DateInterval;
use DatePeriod;
use DateTime;

class SalesCustomerHelper extends Venturo
{
    private $dates;
    private $endDate;
    private $sales;
    private $startDate;
    private $total;
    private $totalPerDate;

    public function __construct()
    {
        $this->sales = new SalesModel();
    }

    public function getDetail($customerId, $date)
    {
        $sales = $this->sales->getSalesByCustomerPerDate($customerId, $date);
        return $this->reformatReportDetail($sales);
    }

    public function get($startDate, $endDate, $categoryId = '')
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;

        $sales = $this->sales->getSalesByCustomer($startDate, $endDate, $categoryId);

        return [
            'status'         => true,
            'data'           => $this->reformatReport($sales, $startDate, $endDate),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }

    /**
     * Convert category_id, product_id, transaction date to literate array
     *
     * @param array $salesDetail
     * @return array
     */
    private function convertNumericKey($salesDetail)
    {
        $indexSales = 0;

        foreach ($salesDetail as $sales) {
            $list[$indexSales] = [
                'customer_id'    => $sales['customer_id'],
                'customer_name'  => $sales['customer_name'],
                'customer_total' => $sales['customer_total'],
                'transaksi'   => array_values($sales['transaksi']),
            ];

            $indexSales++;
        }

        unset($salesDetail);

        return $list ?? [];
    }

    /**
     * get list date between start and end date
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getPeriode()
    {
        $begin = new DateTime($this->startDate);
        $end   = new DateTime($this->endDate);
        $end   = $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $date         = $dt->format('Y-m-d');
            $dates[$date] = [
                'date_transaction' => $date,
                'total_sales'      => 0,
            ];

            $this->setDefaultTotal($date);
            $this->setSelectedDate($date);
        }

        return $dates ?? [];
    }

    private function reformatReportDetail($list)
    {
        $list        = $list->toArray();
        $salesDetail = [];

        foreach ($list as $sales) {
            $subTotal = 0;
            $discount = 0;
            $noStruk                = $sales['no_struk'];
            $voucher                = $sales['voucher_nominal'];
            foreach ($sales['details'] as $detail) {
                $subTotal               += $detail['price'] * $detail['total_item'];
                $discount               += $detail['discount_nominal'];
            }
            $tax                    = $subTotal * 0.11;
            $total                  = ($subTotal + $tax - $voucher - $discount) <0 ? 0 : ($subTotal + $tax - $voucher - $discount);
            $salesDetail['transaksi'][] = [
                'no_struk'      => $noStruk,
                'subtotal'      => $subTotal,
                'tax'           => $tax,
                'voucher'       => $voucher,
                'discount'      => $discount,
                'total'         => $total,
            ];
            $salesDetail['totalPerDate'] = ($salesDetail['totalPerDate'] ?? 0) + $total;
        }

        return $salesDetail;
    }

    /**
     * Reformat data from db to multidimensional array based on category => product => transactions
     *
     * @param object $list
     * @return array
     */

     private function reformatReport($list)
    {
        $list        = $list->toArray();
        $periods     = $this->getPeriode();
        $salesDetail = [];
        
        foreach ($list as $sales) {
            $date                   = date('Y-m-d', strtotime($sales['date']));
            $customerId             = $sales['m_customer_id'];
            $customerName           = $sales['customer']['name'];
            $subTotal = 0;
            $totalSales = 0;
            $discountNominal = 0;
            foreach ($sales['details'] as $detail) {
                $discountNominal        += $detail['discount_nominal'];
                $subTotal               += ($detail['price'] * $detail['total_item']);
    
            }
            $totalPerCustomer       = $salesDetail[$customerId]['customer_total'] ?? 0;
            $totalSales             = ($subTotal + ($subTotal * 0.11)) - $sales['voucher_nominal'] - $discountNominal;
            $listTransactions       = $salesDetail[$customerId]['transaksi'] ?? $periods;
            
            $salesDetail[$customerId] = [
                'customer_id'    => $customerId,
                'customer_name'  => $customerName,
                'customer_total' => ($totalPerCustomer + $totalSales) <0 ? 0 : $totalPerCustomer + $totalSales,
                'transaksi'      => $listTransactions,
            ];
            $totalPerDate       = $salesDetail[$customerId]['transaksi'][$date]['total_sales'] ?? 0;
            $salesDetail[$customerId]['transaksi'][$date] = [
                'date_transaction' => $date,
                'total_sales'      => ($totalPerDate + $totalSales) <0 ? 0 : $totalPerDate + $totalSales,
            ];

            $this->totalPerDate[$date] = ($this->totalPerDate[$date] ?? 0) + ($totalSales <0 ? 0 : $totalSales) ;
            $this->total               = ($this->total ?? 0) + ($totalSales <0 ? 0 : $totalSales);
        }
     
        return $this->convertNumericKey($salesDetail);
    }

    /**
     * Set default value of grand total for every date
     *
     * @param string $date
     * @return array
     */
    private function setDefaultTotal(string $date)
    {
        $this->totalPerDate[$date] = 0;
    }

    /**
     * Set selected / filtered dates
     *
     * @param string $date
     * @return array
     */
    private function setSelectedDate(string $date)
    {
        $this->dates[] = $date;
    }
}
