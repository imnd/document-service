<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class CopyDocumentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type' => 'required|string|in:' . config('entities.types.document'),
        ];
    }
}
