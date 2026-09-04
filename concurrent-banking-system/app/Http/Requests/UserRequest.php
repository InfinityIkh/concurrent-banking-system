<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => [
                $this->is('*/login') ? '' : 'required',
                'string',
                'max:255'
            ],
            'lastname' => [
                $this->is('*/login') ? '' : 'required',
                'string',
                'max:255',
            ],
            'email' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'required',
                'string',
                'email',
                'max:255',
                $this->is('*/login') ? '' : 'unique:users'
            ],
            'password' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'string',
                'min:8',
                $this->is('*/register') ? 'confirmed' : ''
            ],
        ];
    }
}
