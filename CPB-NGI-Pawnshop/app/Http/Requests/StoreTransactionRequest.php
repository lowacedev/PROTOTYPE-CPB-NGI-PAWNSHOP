<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'       => 'required|exists:customers,id',
            'loan_amount'       => 'required|numeric|min:0.01',
            'interest_rate'     => 'required|numeric|min:0|max:100',
            'term_days'         => 'required|integer|min:1',
            'items'             => 'required|array|min:1',
            'items.*.item_id'   => 'required|exists:items,id',
            'items.*.quantity'  => 'required|integer|min:1',
            'notes'             => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'         => 'You must add at least one item to pawn.',
            'items.min'              => 'You must add at least one item to pawn.',
            'items.*.item_id.required' => 'Please select an item.',
            'items.*.item_id.exists' => 'Selected item does not exist.',
        ];
    }
}
