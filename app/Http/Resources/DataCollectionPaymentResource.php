<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataCollectionPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_type_id' => $this->payment_type_id,
            'transaction_id' => $this->transaction_id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,

            'payment_type' => PaymentTypeResource::make($this->paymentType),
            'transaction' => DataCollectionResource::make($this->transaction),
        ];
    }
}
