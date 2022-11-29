<?php

namespace App\Http\Requests\Entity;

use App\Contracts\UpdateEntityRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDescriptionRequest extends FormRequest implements UpdateEntityRequestContract
{

    protected $payloadParam = 'payload';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // TODO: добавить валидацию логических выражений: проверить наличие id деревьев в базе и корректность выражения

        $textsRules = [];
        foreach (config('documents.locales') as $locale) {
            $textsRules[$this->payloadParam . '.title.' . $locale] = 'filled|string';
        }

        return array_merge([
            'type'    => 'required|string|in:'.config('entities.types.description'),
            'user_id' => 'integer',

            $this->payloadParam.'.title' => 'required|array',

            $this->payloadParam.'.content_id' => 'filled|exists:contents,id',
            $this->payloadParam.'.matrixes' => 'present|array',

            $this->payloadParam.'.matrixes.*.id'        => 'filled|integer|exists:entities,id,type,'.config('entities.types.matrix'),
            $this->payloadParam.'.matrixes.*.version'   => 'filled|integer|required_with:'. $this->payloadParam .'.matrixes.*.id',
            $this->payloadParam.'.matrixes.*.user_id'   => 'integer',
            $this->payloadParam.'.matrixes.*.type'      => 'filled|in:'.implode(',', config('entities.matrix_types')),
            $this->payloadParam.'.matrixes.*.main_id'   => 'nullable|integer|exists:entities,id,type,'.config('entities.types.matrix'),
            $this->payloadParam.'.matrixes.*.is_number' => 'boolean',
            $this->payloadParam.'.matrixes.*.is_user'   => 'boolean',

            $this->payloadParam.'.matrixes.*.children'  => 'present|array',
            $this->payloadParam.'.matrixes.*.children.*.id' => 'integer|different:'. $this->payloadParam .'.matrixes.*.id|exists:entities,id,type,'.config('entities.types.matrix'),


        ], $textsRules);
    }
}
