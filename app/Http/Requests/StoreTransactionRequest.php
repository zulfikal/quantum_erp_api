<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'transaction_method_id' => 'required|exists:transaction_methods,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'company_bank_id' => 'required|exists:company_banks,id',
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
