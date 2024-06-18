<?php

namespace App\Http\Requests\Api\DataCollectorActions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddProductStoreRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_collection_id' => ['required', Rule::exists('data_collections', 'id')->whereNull('deleted_at')],
            'sku_or_alias' => 'required|string|exists:products_aliases,alias',
            'quantity_scanned' => 'sometimes|numeric',
            'quantity_requested' => 'sometimes|numeric',
        ];
    }
}
