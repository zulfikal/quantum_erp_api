<?php

namespace App\Http\Requests;

use App\Models\Product\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        // Get the product ID from the route if it exists (for update operations)
        $productId = $this->route('product') ? $this->route('product')->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'type' => 'required|in:goods,service',
            'sku' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($productId) {
                    if (!$value) return; // Skip validation if SKU is null
                    
                    $companyId = auth()->user()->employee->company->id;
                    $query = \App\Models\Product\Product::where('company_id', $companyId)
                        ->where('sku', $value);
                    
                    // Exclude current product when updating
                    if ($productId) {
                        $query->where('id', '!=', $productId);
                    }
                    
                    if ($query->exists()) {
                        $fail('The SKU has already been taken within your company.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'alert_stock' => 'required|boolean',
            'alert_stock_threshold' => 'required|numeric',
            'is_active' => 'required|boolean',
        ];
    }
}