<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 02.11.2018
 * Time: 16:11
 */

namespace App\Http\Filters\EntityData;


use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class VersionFilter implements Filter
{
    
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query
            ->when(! empty($value), function (Builder $query) use ($value) {
                $query->whereHas('data', function (Builder $data) use ($value){
                    $data->where('version', $value);
                });
            });
    }
}