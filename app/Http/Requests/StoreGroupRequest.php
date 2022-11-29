<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
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
        $textsRules = [];
        foreach (config('group.locales') as $locale) {
            $textsRules['title.'.$locale]       = 'nullable|string';
            $textsRules['placeholder.'.$locale] = 'nullable|string';
            $textsRules['description.'.$locale] = 'nullable|string';
        }

        return array_merge([
            'title'       => 'required|array',
            'title.*'     => 'required',
            'placeholder' => 'filled|array',
            'description' => 'filled|array',
            'type'        => 'required|in:'. implode(',', config('group.types')),
            'options'     => 'filled|array',
            'fields'      => 'array'
        ], $textsRules);

    }
}
