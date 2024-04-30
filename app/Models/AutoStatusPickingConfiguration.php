<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\AutoStatusPickingConfiguration.
 *
 * @property int $id
 * @property int $max_batch_size
 * @property int $max_order_age
 * @property-read int     $current_count_with_status
 * @property-read int $required_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|AutoStatusPickingConfiguration newModelQuery()
 * @method static Builder|AutoStatusPickingConfiguration newQuery()
 * @method static Builder|AutoStatusPickingConfiguration whereCreatedAt($value)
 * @method static Builder|AutoStatusPickingConfiguration whereId($value)
 * @method static Builder|AutoStatusPickingConfiguration whereMaxBatchSize($value)
 * @method static Builder|AutoStatusPickingConfiguration whereMaxOrderAge($value)
 * @method static Builder|AutoStatusPickingConfiguration whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AutoStatusPickingConfiguration extends Model
{
    use HasFactory;

    protected $table = 'modules_autostatus_picking_configurations';

    /**
     * @var string[]
     */
    protected $fillable = [
        'max_batch_size',
        'max_order_age',
    ];

    /**
     * @return int
     */
    public function getRequiredCountAttribute(): int
    {
        return $this->max_batch_size - $this->current_count_with_status;
    }

    /**
     * @return int
     */
    public function getCurrentCountWithStatusAttribute(): int
    {
        return Order::whereIn('status_code', ['picking'])->count();
    }
}
