<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 22.11.2018
 * Time: 17:50
 */

namespace App\Http\Filters\Npa;


use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class NpaTitleFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(',', $value) : $value;

        return $query
            ->when($value, function (Builder $query) use ($value) {
                $query->where('title->'. config('app.locale'), 'like', "%$value%");
            });
    }
}
