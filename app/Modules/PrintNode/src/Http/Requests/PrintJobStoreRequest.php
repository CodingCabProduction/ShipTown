<?php

namespace App\Modules\PrintNode\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrintJobStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'printer_id'    => ['int', 'required'],
            'title'         => ['string', 'sometimes'],
            'content_type'  => ['string', 'sometimes'],
            'content'       => ['string', 'sometimes'],
            'expire_after'  => ['int', 'sometimes'],
            'pdf_url'       => ['sometimes'],
        ];
    }
}
