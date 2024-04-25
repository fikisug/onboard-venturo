<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sale\SalesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\SalesRequest;
use App\Http\Resources\Sale\SalesCollection;
use App\Http\Resources\Sale\SalesResource;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    private $sales;
    public function __construct()
    {
        $this->sales = new SalesHelper();
    }

    public function destroy($id)
    {
        $sale = $this->sales->delete($id);

        if (!$sale['status']) {
            return response()->failed(['Mohon maaf product tidak ditemukan']);
        }

        return response()->success($sale, 'product berhasil dihapus');
    }

    public function index(Request $request)
    {
        $filter = [
            // 'name' => $request->name ?? '',
            // 'm_product_category_id' => $request->product_category_id ?? '',
            // 'is_available' => isset($request->is_available) ? $request->is_available : '',
        ];
        $sale = $this->sales->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new SalesCollection($sale['data']));
    }

    public function show($id)
    {
        $sale = $this->sales->getById($id);

        if (!($sale['status'])) {
            return response()->failed(['Data product tidak ditemukan'], 404);
        }

        return response()->success(new SalesResource($sale['data']));
    }


    public function store(SalesRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'no_struk',
            'm_customer_id',
            'm_voucher_id',
            'voucher_nominal',
            'total_voucher',
            'm_discount_id',
            'date',     
            'details'            
        ]);
        // $payload['m_product_category_id'] = $payload['product_category_id'];
        $sale = $this->sales->create($payload);

        if (!$sale['status']) {
            return response()->failed($sale['error']);
        }

        return response()->success(new SalesResource($sale['data']), 'product berhasil ditambahkan');
    }

    public function update(SalesRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'no_struk',
            'm_customer_id',
            'm_voucher_id',
            'voucher_nominal',
            'm_discount_id',
            'date',     
            'details',
            'details_deleted'
        ]);
        // $payload['m_product_category_id'] = $payload['product_category_id'];
        $sale = $this->sales->update($payload, $payload['id'] ?? 0);

        if (!$sale['status']) {
            return response()->failed($sale['error']);
        }

        return response()->success(new SalesResource($sale['data']), 'product berhasil diubah');
    }
}
