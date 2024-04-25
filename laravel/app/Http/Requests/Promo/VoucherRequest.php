<?php

namespace App\Http\Requests\Promo;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class VoucherRequest extends FormRequest
{
    use ConvertsBase64ToFiles; 
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public $validator;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    protected function base64FileKeys():array
    {
        return [
            'photo' => 'foto-promo.jpg',
        ];
    }

    private function createRules(): array
    {
        return [
            'customer_id' => 'required',
            'promo_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_voucher' => 'required|numeric',
            'nominal_rupiah' => 'required|numeric',
            'photo' => 'nullable|file|image',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id' => 'required',
            'customer_id' => 'required',
            'promo_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'total_voucher' => 'required|numeric',
            'nominal_rupiah' => 'required|numeric',
            // 'photo' => 'nullable|file|image',
        ];
    }

    public function attributes()
    {
        return [
            'customer_id' => 'Customer',
            'promo_id' => 'Voucher',
            'nominal_rupiah' => 'Nominal',
        ];
    }
}
