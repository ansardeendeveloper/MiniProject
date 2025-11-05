<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow request
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:service_records,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Paid,Unpaid,Pending',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required.',
            'service_id.required' => 'Service record is required.',
            'invoice_date.required' => 'Invoice date is required.',
            'total_amount.required' => 'Total amount is required.',
            'status.in' => 'Status must be Paid, Unpaid, or Pending.',
        ];
    }
}
