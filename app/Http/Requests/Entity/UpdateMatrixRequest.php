<?php

namespace App\Http\Requests\Entity;

use App\Contracts\UpdateEntityRequestContract;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatrixRequest extends FormRequest implements UpdateEntityRequestContract
{

    protected $payloadParam = 'payload';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $textsRules = [];
        foreach (config('documents.locales') as $locale) {
            $textsRules[$this->payloadParam.'.text.'.$locale] = 'filled|string';
        }

        return array_merge([
            'user_id'      => 'filled|integer',
            'type'         => [
                'required',
                'string',
                Rule::in(config('entities.types'))
            ],
            'main_id'      => 'filled|integer|exists:entities,id,type,'.config('entities.types.matrix').'|not_in:'.request('entity'),
            $this->payloadParam.'.text' => 'required|array',
        ], $textsRules);
    }
}
