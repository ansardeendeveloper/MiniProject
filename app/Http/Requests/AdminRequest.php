<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow validation to run
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:admins,email,' . ($this->admin->id ?? 'null'),
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required_with:password|string|min:6',
            'phone' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'address' => 'required|string|max:255',
            'role' => 'required|in:admin,superadmin',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Admin name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be valid and can include an optional "+" with 10–15 digits.',
            'address.required' => 'Address is required.',
            'role.required' => 'Role is required.',
            'role.in' => 'Role must be either admin or superadmin.',
        ];
    }
}
