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
            'quotation.quotation_number' => 'nullable|string|max:255',
            'quotation.sale_status_id' => 'required|exists:sale_statuses,id',
            'quotation.quotation_date' => 'required|date',
            'quotation.notes' => 'nullable|string',
            'quotation.description' => 'required|string',
            'quotation.shipping_amount' => 'required|numeric',
            'customer.customer_id' => 'nullable|exists:customers,id',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'nullable|email',
            'customer.phone' => 'nullable|string|max:255',
            'customer.address_1' => 'nullable|string',
            'customer.address_2' => 'nullable|string',
            'customer.city' => 'nullable|string|max:255',
            'customer.state' => 'nullable|string|max:255',
            'customer.zip_code' => 'nullable|string|max:255',
            'customer.country' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.type' => 'required|in:goods,service',
            'items.*.sku' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.tax_percentage' => 'nullable|numeric',
        ];
    }
}
