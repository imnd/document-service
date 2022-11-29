<?php

namespace App\Http\Filters\EntityData;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TypeFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->where('type', $value);
    }
}
