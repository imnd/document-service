<?php

namespace App\Http\Filters\Npa;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class NpaLinkPayloadFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query
            ->when($value, function (Builder $query) use ($value, $property) {
                $query->where('payload->'. $property, $value);
            });
    }
}
