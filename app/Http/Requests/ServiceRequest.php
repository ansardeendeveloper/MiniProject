<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id'             => ['required', 'exists:vehicles,id'],
            'vehicle_number'         => ['required', 'string', 'max:20'],
            'vehicle_name'           => ['required', 'string', 'max:100'],
            'manufacturer'           => ['required', 'string', 'max:100'],
            'year'                   => ['required', 'integer', 'digits:4', 'min:1900', 'max:' . date('Y')],
            'customer_name'          => ['required', 'string', 'max:100'],
            'mobile_number'          => ['required', 'regex:/^[0-9]{10,15}$/'],
            'km_run'                 => ['required', 'integer', 'min:1'],
            'service_start_datetime' => ['required', 'date'],
            'service_types'          => ['required', 'array', 'min:1'],
            'service_types.*'        => ['string', 'max:255'],
            'amount'                 => ['required', 'numeric', 'min:0'],
            'status'                 => ['required', 'in:pending,completed,in_progress'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile_number.regex' => 'Mobile number must contain 10–15 digits.',
            'service_types.required' => 'Select at least one service type.',
        ];
    }
}
