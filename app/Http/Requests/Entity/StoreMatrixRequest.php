<?php

namespace App\Http\Requests\Entity;

use App\Contracts\StoreEntityRequestContract;

class StoreMatrixRequest extends UpdateMatrixRequest implements StoreEntityRequestContract
{

    protected $payloadParam = 'new';
}
