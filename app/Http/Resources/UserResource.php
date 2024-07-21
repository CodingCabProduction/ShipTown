<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->getKey(),
            'name'                    => $this->name,
            'email'                   => $this->email,
            'warehouse_id'            => $this->warehouse_id,
            'warehouse_code'          => $this->warehouse_code,
            'default_dashboard_uri'   => $this->default_dashboard_uri,
            'location_id'             => $this->location_id,
            'printer_id'              => $this->printer_id,
            'address_label_template'  => $this->address_label_template,
            'ask_for_shipping_number' => $this->ask_for_shipping_number,
            'active_transaction_id'   => $this->active_transaction_id,

            'roles'                   => RoleResource::collection($this->whenLoaded('roles')),
            'warehouse'               => WarehouseResource::make($this->whenLoaded('warehouse')),
            'sessions'                => SessionResource::collection($this->whenLoaded('sessions')),
        ];
    }
}
