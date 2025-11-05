<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Enable authorization
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'recipient_type' => 'required|string|in:customer,staff,all',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Notification title is required.',
            'message.required' => 'Notification message is required.',
            'recipient_type.in' => 'Recipient type must be customer, staff, or all.',
        ];
    }
}
