<?php

namespace App\Http\Requests\Customer;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class CustomerUpdateRequest extends FormRequest
{
    use ConvertsBase64ToFiles; // Library untuk convert base64 menjadi File
    
    public $validator = null;

    /**
     * Tampilkan pesan error ketika validasi gagal
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
       $this->validator = $validator;
    }

    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-customer.jpg',
        ];
    }


    public function rules()
    {
        $userId = $this->input('id');
        
        return [
            'id' => 'required',
            'name' => 'required|max:100',
            'photo' => 'nullable|file|image', // Validasi untuk upload file image saja, jika tidak ada perubahan foto user, isi key foto dengan NULL
            'email' => ['nullable','email',Rule::unique('m_customer')->ignore($userId)],
            'date_of_birth' => 'nullable|date',
            'phone_number' => 'nullable|numeric',
            'is_verified' => 'nullable'
        ];
    }
}
