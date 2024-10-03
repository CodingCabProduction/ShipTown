<?php

namespace App\Http\Requests\DataCollectionPayment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_type_id' => ['sometimes', 'numeric'],
            'amount' => ['sometimes', 'numeric'],
        ];
    }
}
