<?php

namespace App\Http\Filters\EntityData;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class UserIdFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query
            ->whereHas('data', function ($data) use ($value){ /* @var Builder $data */
                $data
                    ->when(empty($value), function (Builder $query) {
                        $query->whereNull('user_id');
                    })
                    ->when(! empty($value), function (Builder $query) use ($value) {
                        $query->where('user_id', $value);
                    });
            });
    }
}
