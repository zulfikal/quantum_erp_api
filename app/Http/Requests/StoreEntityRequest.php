<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntityRequest extends FormRequest
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
            'entity.name' => 'required|string|max:255',
            'entity.type' => 'required|in:customer,supplier',
            'entity.status' => 'nullable|in:active,inactive',
            'entity.entity_id' => 'required|string|max:255',
            'entity.tin_number' => 'nullable|string|max:255',
            'entity.website' => 'nullable|string|max:255',
            'entity.notes' => 'nullable|string',
            'address' => 'required|array',
            'address.*.address_1' => 'required|string|max:255',
            'address.*.address_2' => 'nullable|string|max:255',
            'address.*.city' => 'required|string|max:255',
            'address.*.state' => 'required|string|max:255',
            'address.*.zip_code' => 'required|string|max:255',
            'address.*.country' => 'required|string|max:255',
            'address.*.notes' => 'nullable|string',
            'address.*.type' => 'nullable|in:billing,shipping,billing_and_shipping',
            'address.*.is_default' => 'nullable|boolean',
            'contacts' => 'required|array',
            'contacts.*.type' => 'required|in:phone,mobile,email,fax,other',
            'contacts.*.value' => 'required|string|max:255',
            'contacts.*.notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'entity.name.required' => 'Business partner name is required',
            'entity.type.required' => 'Business partner type is required',
            'entity.entity_id.required' => 'Business partner register number / nric is required',
            'address.*.address_1.required' => 'Address is required',
            'address.*.city.required' => 'City is required',
            'address.*.state.required' => 'State is required',
            'address.*.zip_code.required' => 'Zip code is required',
            'address.*.country.required' => 'Country is required',
            'contacts.required' => 'Contacts is required',
            'contacts.*.type.required' => 'Contact type is required',
            'contacts.*.value.required' => 'Contact value is required',
        ];
    }
}
