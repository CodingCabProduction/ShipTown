<?php

namespace App;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel.
 *
 * @mixin Eloquent
 */
abstract class BaseModel extends Model
{
    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isAttributeChanged(string $attribute): bool
    {
        return $this->getOriginal($attribute) !== $this->getAttribute($attribute);
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isAttributeNotChanged(string $attribute): bool
    {
        return !$this->isAttributeChanged($attribute);
    }

    /**
     * @param array $attributes
     *
     * @return bool
     */
    public function isAnyAttributeChanged(array $attributes): bool
    {
        $changedAttributes = collect($attributes)->filter(function (string $attribute) {
            return $this->isAttributeChanged($attribute);
        });

        return $changedAttributes->isNotEmpty();
    }
}
