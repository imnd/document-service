<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 23.11.2018
 * Time: 14:11
 */

namespace App\Http\Filters\Field;


use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class GroupTitleFilter implements Filter
{
    
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query
            ->when($value, function (Builder $query) use ($value){
                $query->where('title->'. config('app.locale'), 'like', "%$value%");
            });
    }
}