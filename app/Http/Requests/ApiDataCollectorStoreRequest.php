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
            'type' => ['sometimes', 'string', 'in:App\\Models\\DataCollectionTransferIn'],
            'destination_warehouse_id' => ['sometimes', 'required',  'integer', 'exists:warehouses,id'],
        ];
    }
}
