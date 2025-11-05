<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|max:100|unique:owners,email',
            'phone'           => 'required|string|max:15|unique:owners,phone',
            'vehicle_number'  => 'required|string|max:50|unique:owners,vehicle_number',
            'address'         => 'required|string|max:255',
            'password'        => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_#^]).+$/',
                'confirmed'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must include uppercase, lowercase, digit, and special character.',
        ];
    }
}
