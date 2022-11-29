<?php

namespace App\Http\Filters\Npa;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class NpaPayloadFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query
            ->when($value, function (Builder $query) use ($value, $property) {
                $query->whereHas('links', function (Builder $npaLink) use ($value, $property) {
                    $npaLink->where('payload->' . $property, $value);
                });
            });
    }
}
