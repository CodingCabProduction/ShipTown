<?php

namespace App\Http\Resources;

use App\Models\Heartbeat;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class HeartbeatResources
 *
 * @mixin Heartbeat
 */
class HeartbeatResources extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'code'              => $this->code,
            'expires_at'        => $this->expires_at,
            'error_message'     => $this->error_message,
            'auto_heal_job_class' => $this->auto_heal_job_class,
        ];
    }
}
