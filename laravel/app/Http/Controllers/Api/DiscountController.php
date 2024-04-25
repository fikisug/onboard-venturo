<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Promo\DiscountHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Promo\DiscountRequest;
use App\Http\Resources\Promo\DiscountCollection;
use App\Http\Resources\Promo\DiscountResource;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    private $discount;
    public function __construct()
    {
        $this->discount = new DiscountHelper();
    }

    public function destroy($id)
    {
        $discounts = $this->discount->delete($id);

        if (!$discounts) {
            return response()->failed(['Mohon maaf voucher tidak ditemukan']);
        }

        return response()->success($discounts, 'voucher berhasil dihapus');
    }

    public function index(Request $request)
    {
        $filter = [
            'm_customer_id' => isset($request->customer_id) ? explode(',', $request->customer_id) : [],
        ];
        $discounts = $this->discount->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new DiscountCollection($discounts['data']));
    }


    public function show($id)
    {
        $discounts = $this->discount->getById($id);

        if (!($discounts['status'])) {
            return response()->failed(['Data voucher tidak ditemukan'], 404);
        }

        return response()->success(new DiscountResource($discounts['data']));
    }

    public function store(DiscountRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['customer_id', 'promo_id']);
        $payload = $this->renamePayload($payload);
        $discounts = $this->discount->create($payload);

        if (!$discounts['status']) {
            return response()->failed($discounts['error']);
        }

        return response()->success(new DiscountResource($discounts['data']), 'voucher berhasil ditambahkan');
    }

    public function update(DiscountRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'customer_id', 'promo_id']);
        $payload = $this->renamePayload($payload);
        $discounts = $this->discount->update($payload, $payload['id'] ?? 0);

        if (!$discounts['status']) {
            return response()->failed($discounts['error']);
        }

        return response()->success(new DiscountResource($discounts['data']), 'voucher berhasil diubah');
    }

    public function renamePayload($payload)
    {
        $payload['m_customer_id'] = $payload['customer_id'] ?? null;
        $payload['m_promo_id'] = $payload['promo_id'] ?? null;
        unset($payload['customer_id']);
        unset($payload['promo_id']);
        return $payload;
    }
}
