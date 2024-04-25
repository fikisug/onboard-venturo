<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
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

    private function createRules(): array
    {
        return [
            'name' => 'required|max:150',
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => 'required|max:150',
            'id' => 'required'
        ];
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }
}
