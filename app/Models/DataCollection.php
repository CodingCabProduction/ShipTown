<?php

namespace App\Models;

use App\BaseModel;
use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *  DataCollection
 * @property int $id
 * @property int $warehouse_id
 * @property int $destination_warehouse_id
 * @property int $destination_collection_id
 * @property string $name
 * @property string $custom_uuid
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property HasMany $records
 * @property Warehouse $warehouse
 * @property DataCollection $destinationCollection
 * @property string $type
 * @property string $currently_running_task
 *
 */
class DataCollection extends BaseModel
{
    use HasFactory;

    use SoftDeletes;
    use LogsActivityTrait;

    protected static $logAttributes = [
        'deleted_at',
        'type',
        'currently_running_task'
    ];

    protected $fillable = [
        'type',
        'warehouse_id',
        'destination_warehouse_id',
        'destination_collection_id',
        'name',
        'custom_uuid',
        'currently_running_task'
    ];

    public function records(): HasMany
    {
        return $this->hasMany(DataCollectionRecord::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function destinationCollection(): BelongsTo
    {
        return $this->belongsTo(DataCollection::class, 'destination_collection_id');
    }

    /**
     * @return HasMany|DataCollectionComment
     */
    public function comments(): HasMany|DataCollectionComment
    {
        return $this->hasMany(DataCollectionComment::class)->orderByDesc('id');
    }
}
