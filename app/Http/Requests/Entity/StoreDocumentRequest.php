<?php

namespace App\Http\Requests\Entity;

use App\Contracts\StoreEntityRequestContract;

class StoreDocumentRequest extends UpdateDocumentRequest implements StoreEntityRequestContract
{
    protected $payloadParam = 'new';

    public function rules()
    {
        return array_merge(parent::rules(), [
            //$this->payloadParam.'.document_version' => 'required|integer', TODO добавить exists
            $this->payloadParam . '.constructor_id' => 'required|exists:entities,id,type,' . config('entities.types.constructor'),
            $this->payloadParam . '.user_id'        => 'integer',
        ]);
    }
}
