<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id;

        return [
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'nullable|email|unique:customers,email,' . $customerId,
            'phone_number' => 'required|string|max:20',
            'region_id'    => 'required|exists:regions,id',
            'province_id'  => 'required|exists:provinces,id',
            'city_id'      => 'required|exists:cities,id',
            'barangay_id'  => 'required|exists:barangays,id',
            'address_line' => 'nullable|string|max:500',
            'id_type'      => 'required|in:national_id,passport,driver_license,sss,philhealth,voters_id',
            'id_number'    => 'required|string|unique:customers,id_number,' . $customerId,
            'id_image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes'        => 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ];
    }
}
