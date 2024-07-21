<?php

namespace App\Http\Requests\PrintJob;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'printer_id' => 'required',
            'content' => 'required|string',
        ];
    }
}
