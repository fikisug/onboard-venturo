<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesDetailModel;
use DateTime;

class TotalSalesHelper extends Venturo
{
    private $sales;

    public function __construct()
    {
        $this->sales = new SalesDetailModel();
    }

    public function getTotalPerMonths($year): array
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March',
            4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September',
            10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $diagram = [];
        foreach ($months as $month => $name) {
            $total                    = $this->sales->getTotalPerMonth($month, $year);
            $diagram['label'][]       = $name;
            $diagram['data'][]        = $total;
        }

        return [
            'status' => true,
            'data'   => $diagram
        ];
    }

    /**
     * @throws Exception
     */
    public function getTotalPerCustomDate($startDate = null, $endDate = null): array
    {
        $dates = ['startDate' => $startDate, 'endDate' => $endDate];

        $listSales = $this->sales->getTotalPerDates($dates);
        $diagram = [];

        foreach ($listSales as $sale) {
            $isSameYear = $this->isSameYear($startDate, $endDate);

            $diagram['label'][] = $this->formatSaleDate($isSameYear, $sale['saleDate']);
            $diagram['data'][] = $sale['totalSale'];
        }

        return [
            'status' => true,
            'data'   => $diagram
        ];
    }

    /**
     * @throws Exception
     */
    private function isSameYear($startDate, $endDate): bool
    {
        $startDateTime = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);

        return $startDateTime->format('Y') === $endDateTime->format('Y');
    }

    /**
     * @throws Exception
     */
    private function formatSaleDate($isSameYear, $date): string
    {
        $dateTime = new DateTime($date);
        $dateFormat = $isSameYear ? 'd M' : 'd M Y';

        return $dateTime->format($dateFormat);
    }

    /**
     * Get summary of sales today, yesterday, this month, last month
     *
     * @return array
     */
    public function getTotalInPeriode()
    {
        return [
            'status'  => true,
            'data'    => [
                'today'      => $this->getTotalToday(),
                'yesterday'  => $this->getTotalYesterday(),
                'this_month' => $this->getTotalThisMonth(),
                'last_month' => $this->getTotalLastMonth(),
            ]
        ];
    }

    /**
     * Get total sales group by year
     *
     * @return void
     */
    public function getTotalPerYear()
    {
        $years   = $this->sales->getListYear();
        sort($years);

        $diagram = [];
        foreach ($years as $year) {
            $total                    = $this->sales->getTotalPerYears($year);
            $diagram['label'][]       = (string) $year;
            $diagram['data'][]        = $total;
        }

        return [
            'status' => true,
            'data'   => $diagram ?? []
        ];
    }

    /**
     * Get total sales last month
     *
     * @return int
     */
    private function getTotalLastMonth()
    {
        $startDate = new DateTime();
        $start     = $startDate->modify('first day of last month')
            ->format('Y-m-d');

        $endDate   = new DateTime();
        $end       = $endDate->modify('last day of last month')
            ->format('Y-m-d');

        return $this->sales->getTotalSaleByPeriode((string) $start, (string) $end);
    }

    /**
     * Get total sales this month
     *
     * @return int
     */
    private function getTotalThisMonth()
    {
        $startDate = new DateTime();
        $start     = $startDate->modify('first day of this month')
            ->format('Y-m-d');

        $endDate   = new DateTime();
        $end       = $endDate->modify('last day of this month')
            ->format('Y-m-d');

        return $this->sales->getTotalSaleByPeriode((string) $start, (string) $end);
    }

    /**
     * Get total sales today
     *
     * @return int
     */
    private function getTotalToday()
    {
        return $this->sales->getTotalSaleByPeriode((string) date('Y-m-d'), (string) date('Y-m-d'));
    }

    /**
     * Get total sales yesterday
     *
     * @return int
     */
    private function getTotalYesterday()
    {
        $date = new DateTime();
        $date->modify('-1 day');

        return $this->sales->getTotalSaleByPeriode((string) $date->format('Y-m-d'), (string) $date->format('Y-m-d'));
    }
}
