<?php

namespace App\Http\Requests\Entity;

use App\Contracts\StoreEntityRequestContract;

class StoreTemplateRequest extends UpdateTemplateRequest implements StoreEntityRequestContract
{

    protected $payloadParam = 'new';

    public function rules()
    {
        return array_merge(parent::rules(), [
                $this->payloadParam.'.document_id'      => 'required|exists:entities,id,type,'.config('entities.types.template'),
                $this->payloadParam.'.document_version' => 'required|integer', //TODO добавить exists
            ]);
    }
}
