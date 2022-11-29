<?php

namespace App\Http\Requests\Entity;

use App\Contracts\StoreEntityRequestContract;
use Illuminate\Validation\Rule;

class StoreDescriptionRequest extends UpdateDescriptionRequest implements StoreEntityRequestContract
{

    protected $payloadParam = 'new';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'copy_id' => [
                    'filled',
                    Rule::exists('entities', 'id')->where(function ($query) {
                        $query->where('type', config('entities.types.description'));
                    }),
                ],
            ]
        );
    }
}
