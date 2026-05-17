<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePawnWizardRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            // STEP 1: Customer Type
            'customer_type' => 'required|in:existing,new',
            'customer_id'   => 'required_if:customer_type,existing|nullable|exists:customers,id',
            
            // Item Details (Step 2)
            'item_name'        => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'category_id'      => 'required|exists:categories,id',
            'assessed_value'   => 'required|numeric|min:1',
            'condition'        => 'required|in:excellent,good,fair,poor',
            
            // Loan Details (Step 3)
            'loan_percentage' => 'required|numeric|min:1|max:100',
            'loan_amount'     => 'required|numeric|min:1',
            'interest_rate'   => 'required|numeric|min:0|max:100',
            'term_days'       => 'required|integer|min:1|max:365',
        ];

        // If new customer, validate KYC fields
        if ($this->customer_type === 'new') {
            $rules = array_merge($rules, [
                'first_name'   => 'required|string|max:255',
                'middle_name'  => 'nullable|string|max:255',
                'last_name'    => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email'        => 'nullable|email|max:255|unique:customers,email',
                'region_id'    => 'required|exists:regions,id',
                'province_id'  => 'required|exists:provinces,id',
                'city_id'      => 'required|exists:cities,id',
                'barangay_id'  => 'required|exists:barangays,id',
                'address_line' => 'nullable|string|max:255',
                'id_type'      => 'required|in:national_id,passport,driver_license,sss,philhealth,voters_id',
                'id_number'    => 'required|string|max:100',
                'id_image'     => 'nullable|image|max:2048',
                'notes'        => 'nullable|string|max:1000',
            ]);
        }

        return $rules;
    }
}
