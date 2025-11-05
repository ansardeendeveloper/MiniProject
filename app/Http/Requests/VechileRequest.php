<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_no' => 'required|string|max:20|unique:vehicles,registration_no,' . ($this->vehicle->id ?? 'null'),
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'color' => 'required|string|max:50',
            'customer_id' => 'required|exists:customers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'registration_no.required' => 'Vehicle registration number is required.',
            'make.required' => 'Vehicle make is required.',
            'model.required' => 'Vehicle model is required.',
            'year.required' => 'Manufacture year is required.',
            'color.required' => 'Vehicle color is required.',
            'customer_id.exists' => 'Selected customer does not exist.',
        ];
    }
}