<?php

namespace App\Http\Requests;

use App\Contracts\UpdateEntityRequestContract;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEntityRequest extends FormRequest implements UpdateEntityRequestContract
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
            'type' => [
                'required',
                Rule::in(config('entities.types')),
            ],
            'main_id' => Rule::exists('entities', 'main_id')->where(function ($query) {
                $query->where('id', $this->data['main_id'])->where('type', $this->data['type']);
            }),
            'new' => 'required',
        ];
    }
}
