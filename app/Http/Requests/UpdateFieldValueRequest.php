<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldValueRequest extends FormRequest
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
            'field_id'       => 'required|integer',
            'user_value'     => 'required|string',
            'user_id'        => 'required|integer',
            'group_id'       => 'required|integer',
            'group_type'     => 'required|in:'. implode(',', config('group.types')),
        ];
    }
}
