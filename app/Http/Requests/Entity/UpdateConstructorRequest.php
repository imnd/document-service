<?php

namespace App\Http\Requests\Entity;

use App\Contracts\UpdateEntityRequestContract;
use App\Rules\DependencyExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConstructorRequest extends FormRequest implements UpdateEntityRequestContract
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
            $textsRules[$this->payloadParam.'.title.'.$locale] = 'filled|string';
        }

        return array_merge([
            'type'    => 'required|string|in:'.config('entities.types.constructor'),
            'user_id' => 'integer',

            $this->payloadParam.'.title' => 'required|array',

            $this->payloadParam.'.content_id' => 'filled|exists:contents,id',
            $this->payloadParam.'.trees'      => 'present|array',

            $this->payloadParam.'.trees.*.id'      => 'filled|integer|exists:entities,id,type,'.config('entities.types.tree'),
            $this->payloadParam.'.trees.*.version' => 'filled|required|integer',
            $this->payloadParam.'.trees.*.user_id' => 'integer',
            $this->payloadParam.'.trees.*.type'    => 'filled|in:'.implode(',', config('entities.tree_types')),
            $this->payloadParam.'.trees.*.main_id' => 'nullable|integer|exists:entities,id,type,'.config('entities.types.tree'),

            $this->payloadParam.'.trees.*.dependencies'            => 'required|array',
            $this->payloadParam.'.trees.*.dependencies.expression' => 'nullable|string',
            $this->payloadParam.'.trees.*.dependencies.depends'    => 'present|array',
            // вернуть, когда мигрируем новые документы
//            $this->payloadParam.'.trees.*.dependencies.depends.*'  => ['string', new DependencyExistsRule],

            $this->payloadParam.'.trees.*.dependencies.affects_trees'   => 'present|array',
            $this->payloadParam.'.trees.*.dependencies.affects_trees.*' => 'integer|different:'.$this->payloadParam.'.trees.*.id|exists:entities,id,type,'.config('entities.types.tree'),

            $this->payloadParam.'.trees.*.dependencies.affects_matrixes'   => 'present|array',
            $this->payloadParam.'.trees.*.dependencies.affects_matrixes.*' => 'integer|exists:entities,id,type,'.config('entities.types.matrix'),

            $this->payloadParam.'.trees.*.children'      => 'present|array',
            $this->payloadParam.'.trees.*.children.*.id' => 'integer|different:'.$this->payloadParam.'.trees.*.id|exists:entities,id,type,'.config('entities.types.tree'),

            $this->payloadParam.'.matrixes' => 'present|array',

            $this->payloadParam.'.matrixes.*.id'        => 'filled|integer|exists:entities,id,type,'.config('entities.types.matrix'),
            $this->payloadParam.'.matrixes.*.version'   => 'filled|integer|required_with:'.$this->payloadParam.'.matrixes.*.id',
            $this->payloadParam.'.matrixes.*.user_id'   => 'integer',
            $this->payloadParam.'.matrixes.*.type'      => 'filled|in:'.implode(',', config('entities.matrix_types')),
            $this->payloadParam.'.matrixes.*.main_id'   => 'nullable|integer|exists:entities,id,type,'.config('entities.types.matrix'),
            $this->payloadParam.'.matrixes.*.is_number' => 'boolean',
            $this->payloadParam.'.matrixes.*.is_user'   => 'boolean',

            $this->payloadParam.'.matrixes.*.children'      => 'present|array',
            $this->payloadParam.'.matrixes.*.children.*.id' => 'integer|different:'.$this->payloadParam.'.matrixes.*.id|exists:entities,id,type,'.config('entities.types.matrix'),

            $this->payloadParam.'.matrixes.*.dependencies'            => 'required|array',
            $this->payloadParam.'.matrixes.*.dependencies.expression' => 'nullable|string',
            $this->payloadParam.'.matrixes.*.dependencies.depends'    => 'present|array',
            // вернуть, когда мигрируем новые документы
//            $this->payloadParam.'.matrixes.*.dependencies.depends.*'  => ['string', new DependencyExistsRule],
        ], $textsRules);
    }
}
