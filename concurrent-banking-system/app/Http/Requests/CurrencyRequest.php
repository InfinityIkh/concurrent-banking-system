<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CurrencyRequest extends FormRequest
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
            'name' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'string',
                'max:255',
                'unique:currencies,name'
            ],
            'code' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'string',
                'size:3',
                'unique:currencies,code'
            ],
            'exchange_rate' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'numeric'
            ]
        ];
    }
}
