<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldRequest extends FormRequest
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
            'title'       => 'required|array',
            'title.*'     => 'required',
            'placeholder' => 'filled|array',
            'description' => 'filled|array',
            'type'        => 'required|in:' . implode(',', config('fields.types')),
            'options'     => 'filled|array',
        ];
    }
}
