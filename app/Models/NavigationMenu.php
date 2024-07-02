<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class NavigationMenu extends Model
{
    protected $table = 'navigation_menu';

    protected $fillable = [
        'name',
        'group',
        'url',
    ];

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(NavigationMenu::class)
            ->allowedFilters([
                'group'
            ])
            ->allowedSorts([
                'id',
                'group',
                'name'
            ]);
    }
}
