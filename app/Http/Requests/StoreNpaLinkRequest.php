<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNpaLinkRequest extends FormRequest
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
            'npa_id'  => [
                'required',
                'integer',
                Rule::exists('npas', 'id')->whereNull('deleted_at')
            ],
            'link'    => 'nullable|url',
            'payload' => 'required|array',

            'payload.part' => 'filled',

            'payload.section'            => 'filled',
            'payload.section_paragraph'  => 'filled',
            'payload.section_subsection' => 'filled',
            
            'payload.chapter'            => 'filled',
            'payload.chapter_paragraph'  => 'filled',
            'payload.chapter_subsection' => 'filled',
            
            'payload.article'  => 'filled',
            'payload.point'    => 'filled',
            'payload.subpoint' => 'filled',
            'payload.indent'   => 'filled',
            
            'payload.piece'        => 'filled',
            'payload.piece_indent' => 'filled',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validKeys = ['part', 'section', 'section_paragraph', 'section_subsection', 'chapter', 'chapter_paragraph', 'chapter_subsection', 'article', 'point', 'subpoint', 'indent', 'piece', 'piece_indent'];
            $keys = array_keys($this->payload);

            if (array_diff($keys, $validKeys))
                $validator->errors()->add('payload', 'invalid keys');
        });
    }
}
