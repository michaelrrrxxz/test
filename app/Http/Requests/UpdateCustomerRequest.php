<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date',
            'address' => 'sometimes|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:customers,email,' . $this->customer->id,
            'contact_number' => 'sometimes|required|string|max:15',
        ];
    }
}
