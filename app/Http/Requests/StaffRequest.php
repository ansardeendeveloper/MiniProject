<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->staff?->id ?? null;

        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff,email,' . $staffId,
            'address' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'age' => 'nullable|integer|min:18',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'role' => 'nullable|in:staff,admin',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
                'confirmed',
            ],
            'password_confirmation' => 'required_with:password|string|min:8',

            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }
}
