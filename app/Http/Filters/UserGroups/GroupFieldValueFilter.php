<?php

namespace App\Http\Filters\UserGroups;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class GroupFieldValueFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereHas('value_fields', function (Builder $query) use ($value) {
            $value = is_array($value) ? implode(',', $value) : $value;
            $query->where('value', 'like', "%$value%");
        });
    }
}
