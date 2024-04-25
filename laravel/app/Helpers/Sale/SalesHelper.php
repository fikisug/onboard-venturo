<?php

namespace App\Helpers\Sale;

use App\Helpers\Venturo;
use App\Models\SalesDetailModel;
use App\Models\SalesModel;
use App\Models\VoucherModel;
use Throwable;

class SalesHelper extends Venturo
{
    private $sales;
    private $salesDetail;
    private $voucher;

    public function __construct()
    {
        $this->sales = new SalesModel();
        $this->salesDetail = new SalesDetailModel();
        $this->voucher = new VoucherModel();
    }

    public function create(array $payload): array
    {
        try {
            $this->beginTransaction();

            $sale = $this->sales->store($payload);

            if(isset($payload['m_voucher_id'])){
                $this->voucher->edit(['total_voucher' => $payload['total_voucher']], $payload['m_voucher_id']);
            }

            $this->insertUpdateDetail($payload['details'] ?? [], $sale->id);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sale
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    public function delete(string $saleId)
    {
        try {
            $this->beginTransaction();

            $this->sales->drop($saleId);

            $this->salesDetail->dropByProductId($saleId);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $saleId
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $sale = $this->sales->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $sale
        ];
    }


    public function getById(string $id): array
    {
        $sale = $this->sales->getById($id);
        if (empty($sale)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $sale
        ];
    }

    private function deleteDetail(array $details)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            $this->salesDetail->drop($val['id']);
        }
    }


    public function update(array $payload): array
    {
        try {
            $this->beginTransaction();
            
            $this->sales->edit($payload, $payload['id']);

            $this->insertUpdateDetail($payload['details'] ?? [], $payload['id']);
            
            $this->deleteDetail($payload['details_deleted'] ?? []);

            $sale = $this->getById($payload['id']);
            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sale['data']
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }


    private function insertUpdateDetail(array $details, string $saleId)
    {
        if (empty($details)) {
            return false;
        }

        if ($saleId == 0) {
            $latestsale = $this->sales->latest()->first();
            if ($latestsale) {
                $saleId = $latestsale->id;
            }
        }

        foreach ($details as $val) {
            // Insert
                $val['t_sales_id'] = $saleId;
                $this->salesDetail->store($val);

            // // Update
            // if (isset($val['is_updated']) && $val['is_updated']) {
            //     $this->salesDetail->edit($val, $val['id']);
            // }
        }
    }
}
