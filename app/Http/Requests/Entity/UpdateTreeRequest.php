<?php

namespace App\Http\Requests\Entity;

use App\Contracts\UpdateEntityRequestContract;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTreeRequest extends FormRequest implements UpdateEntityRequestContract
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
            'main_id'      => 'filled|integer|exists:entities,id,type,'.config('entities.types.tree').'|not_in:'.request('entity'),
            $this->payloadParam.'.text' => 'required|array',
        ], $textsRules);
    }
}
