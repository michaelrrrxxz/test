<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
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
        'customer_id' => 'required|exists:customers,id',
        'quotation_date' => 'required|date',
        'total_items' => 'required|integer|min:1',
        'grand_total' => 'required|numeric|min:0',

        // Add validation for items array
        'items' => 'required|array|min:1',

        // Validate each item inside the array
        'items.*.product_name' => 'required|string|max:255',
        'items.*.item_description' => 'string|nullable|max:1000',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ];
}

}
