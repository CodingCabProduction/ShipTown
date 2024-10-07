<?php

namespace App\Modules\Webhooks\src\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $table = 'modules_webhooks_pending_webhooks';

    protected $fillable = [
        'model_class',
        'model_id',
        'reserved_at',
        'published_at',
        'sns_message_id',
    ];
}
