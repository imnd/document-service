<?php

namespace App\Http\Requests;

use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserGroupsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'group_id'    => 'required|integer|exists:groups,id',

            'group_type'  => 'required|in:'. implode(',', config('group.types')),
            'user_group'  => 'required|array|filled',

            'user_group.*.field_id'  => 'required|integer|exists:fields,id',
            'user_group.*.value'     => 'required|string',
        ];
    }
}
