<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 23.11.2018
 * Time: 14:11
 */

namespace App\Http\Filters\UserGroups;


use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class GroupTypeFilter implements Filter
{
    
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->where('group_type', $value);
    }
}