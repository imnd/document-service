<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNpaRequest extends FormRequest
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
            'title'   => 'required|array',
            'title.*' => 'required|unique_translation:npas',
            'main_id' => [
                'filled',
                'integer',
                Rule::exists('npas', 'id')->whereNull('main_id'),
            ],
        ];
    }
}
