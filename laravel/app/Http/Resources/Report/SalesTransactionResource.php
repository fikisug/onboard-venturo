<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'no_struk' => $this->sale->no_struk ?? null,
            'customer_name' => $this->sale->customer->name ?? null,
            'date' => $this->sale->date ?? null,
            'discount' => $this->sale->discount->promo->nominal_percentage ?? null,
            'voucher' => $this->sale->voucher->promo->nominal_rupiah ?? null,
            'product_name' => $this->product->name ?? null,
            'total_item' => $this->total_item ?? null,
            'price' => ($this->price ?? 0) + ($this->product_detail->price ?? 0),
            'total' => (($this->price ?? 0) + ($this->product_detail->price ?? 0)) * ($this->total_item ?? 0),
            'total_bayar' => ((($this->price ?? 0) + ($this->product_detail->price ?? 0)) * ($this->total_item ?? 0)) - ($this->discount_nominal ?? 0),
            'discount_nominal' => $this->discount_nominal ?? null,
        ];
    }
}
