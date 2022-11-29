<?php

namespace App\Http\Requests\Entity;

use App\Contracts\ShowEntityRequestContract;
use Illuminate\Foundation\Http\FormRequest;

class ShowRequest extends FormRequest implements ShowEntityRequestContract
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
        return [];
    }
}
