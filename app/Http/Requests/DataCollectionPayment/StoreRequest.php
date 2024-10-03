<?php

namespace App\Http\Requests\DataCollectionPayment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'transaction_id' => ['required', 'numeric'],
            'payment_type_id' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
        ];
    }
}
