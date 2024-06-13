<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiDataCollectorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'name' => ['required', 'string'],
            'type' => ['nullable', 'sometimes', 'string', 'in:App\\Models\\DataCollectionTransferIn,App\\Models\\DataCollectionTransferOut,App\\Models\\DataCollectionStocktake'],
            'destination_warehouse_id' => ['nullable', 'sometimes', 'integer', 'exists:warehouses,id'],
        ];
    }
}
