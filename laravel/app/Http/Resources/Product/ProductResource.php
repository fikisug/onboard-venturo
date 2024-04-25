<?php

namespace App\Http\Resources\Product;

use FontLib\Table\Type\name;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class ProductResource extends JsonResource
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
        'name' => $this->name,
        'price' => $this->price,
        'product_category_id' => $this->m_product_category_id,
        'product_category_name' => $this->category->name,
        'is_available' => (string) $this->is_available,
        'description' => $this->description,
        'photo_url' => !empty($this->photo) ? Storage::disk('public')->url($this->photo) : Storage::disk('public'),
        'details' => ProductDetailResource::collection($this->details),
        'details_deleted' => ProductDetailResource::collection($this->details),
    ];
}


}
