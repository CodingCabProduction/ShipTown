<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\Tag;

/**
 * @property int $id
 * @property int $tag_id
 * @property int $taggable_id
 * @property string $taggable_type
 *
 */
class Taggable extends BaseModel
{
    protected $table = 'taggables';

    protected $fillable = [
        'tag_id',
        'tag_name',
        'taggable_type',
        'taggable_id',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
