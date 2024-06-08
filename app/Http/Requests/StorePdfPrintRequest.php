<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePdfPrintRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => 'sometimes|array',
            'data.labels' => 'sometimes|array',
            'product_sku' => 'sometimes|string',
            'template' => 'required',
            'printer_id' => 'required | integer',
        ];
    }
}
