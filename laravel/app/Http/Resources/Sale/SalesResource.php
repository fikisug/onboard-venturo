<?php

namespace App\Http\Resources\Sale;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
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
            'id' => $this->id,
            'no_struk' => $this->no_struk,
            'm_customer_id' => $this->m_customer_id,
            'm_voucher_id' => $this->m_voucher_id,
            'voucher_nominal' => $this->voucher_nominal,
            'total_voucher' => $this->total_voucher,
            'm_discount_id' => $this->m_discount_id,
            'date' => $this->date,
            'details' => SalesDetailResource::collection($this->details),
        ];
    }
}
