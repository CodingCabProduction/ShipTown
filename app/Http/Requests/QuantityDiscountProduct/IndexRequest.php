<?php

namespace App\Http\Requests\QuantityDiscountProduct;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
//            'quantity_discount_id' => 'sometimes|integer|exists:modules_quantity_discounts,id',
//            'product_id' => 'sometimes',
        ];
    }
}