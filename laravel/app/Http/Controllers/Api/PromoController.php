<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Promo\PromoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Promo\PromoCreateRequest;
use App\Http\Requests\Promo\PromoUpdateRequest;
use App\Http\Resources\Promo\PromoCollection;
use App\Http\Resources\Promo\PromoResource;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    private $promo;
    public function __construct()
    {
        $this->promo = new PromoHelper();
    }

    public function destroy($id)
    {
        $promo = $this->promo->delete($id);

        if (!$promo['status']) {
            return response()->failed(['Mohon maaf product tidak ditemukan']);
        }

        return response()->success($promo, 'product berhasil dihapus');
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'status' => $request->status ?? '',
        ];
        $promo = $this->promo->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new PromoCollection($promo['data']));
    }

    public function show($id)
    {
        $promo = $this->promo->getById($id);

        if (!($promo['status'])) {
            return response()->failed(['Data product tidak ditemukan'], 404);
        }

        return response()->success(new PromoResource($promo['data']));
    }


    public function store(PromoCreateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'name',
            'status',
            'expired_in_day',
            'nominal_percentage',
            'nominal_rupiah',
            'term_conditions',
            'photo'
        ]);

        $promo = $this->promo->create($payload);
        
        if (!$promo['status']) {
            return response()->failed($promo['error']);
        }

        return response()->success(new PromoResource($promo['data']), 'product berhasil ditambahkan');
    }

    public function update(PromoUpdateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'name',
            'status',
            'expired_in_day',
            'nominal_percentage',
            'nominal_rupiah',
            'term_conditions',
            'photo'
        ]);

        $promo = $this->promo->update($payload, $payload['id'] ?? '0');
        
        if (!$promo['status']) {
            return response()->failed($promo['error']);
        }

        return response()->success(new PromoResource($promo['data']), 'product berhasil diubah');
    }
}
