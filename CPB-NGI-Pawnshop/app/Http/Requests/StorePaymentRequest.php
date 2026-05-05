<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|exists:transactions,id',
            'amount_paid'    => 'required|numeric|min:0.01',
            'payment_type'   => 'required|in:interest,redemption,partial',
            'payment_method' => 'required|in:cash,check,card,bank_transfer',
            'notes'          => 'nullable|string',
        ];
    }
}
