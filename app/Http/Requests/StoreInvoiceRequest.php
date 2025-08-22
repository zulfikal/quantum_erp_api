<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'invoice.invoice_number' => 'nullable|string|max:255',
            'invoice.sale_status_id' => 'required|exists:sale_statuses,id',
            'invoice.invoice_date' => 'required|date',
            'invoice.notes' => 'nullable|string',
            'invoice.description' => 'required|string',
            'invoice.shipping_amount' => 'required|numeric',
            'invoice.due_date' => 'nullable|date',
            'customer.entity_id' => 'nullable|exists:entities,id',
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
