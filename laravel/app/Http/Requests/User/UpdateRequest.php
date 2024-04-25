<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class UpdateRequest extends FormRequest
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
            'photo' => 'foto-user.jpg',
        ];
    }


    public function rules()
    {
        $userId = $this->input('id');
        
        return [
            'id' => 'required',
            'name' => 'required|max:100',
            'photo' => 'nullable|file|image',
            'email' => ['required','email',Rule::unique('user_auth')->ignore($userId)],
            'password' => 'min:6',
            'phone_number' => 'numeric',
            'user_roles_id' => 'required'
        ];
    }
}
