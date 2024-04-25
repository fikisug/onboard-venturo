<?php

namespace App\Http\Requests\Sale;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SalesRequest extends FormRequest
{ 
    public $validator;
 
    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
 
 
    public function rules()
    {
        if ($this->isMethod('post')) {
            return $this->createSale();
        }
 
        return $this->updateSale();
    }
 
    private function createSale():array
    {
        return [
            'no_struk' => 'required',
            'm_customer_id' => 'required',
            'm_voucher_id' => 'nullable',
            'voucher_nominal' => 'numeric',
            'm_discount_id' => 'nullable',
            'date' => 'date',
            'details' => 'required',
            'details.*.total_item' => 'numeric',
            'details.*.price' => 'numeric',
            'details.*.discount_nominal' => 'numeric'
        ];
    }
 
    private function updateSale():array
    {
        return [
            'id' => 'required',
            'no_struk' => 'required',
            'm_customer_id' => 'required',
            'm_voucher_id' => 'nullable',
            'voucher_nominal' => 'numeric',
            'm_discount_id' => 'nullable',
            'date' => 'date'
        ];
    }
 
    // public function attributes()
    // {
    //     return [
    //         'is_available' => 'Status',
    //         'product_category_id' => 'Category'
    //     ];
    // }
}
