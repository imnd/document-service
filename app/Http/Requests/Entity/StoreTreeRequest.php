<?php

namespace App\Http\Requests\Entity;

use App\Contracts\StoreEntityRequestContract;

class StoreTreeRequest extends UpdateTreeRequest implements StoreEntityRequestContract
{

    protected $payloadParam = 'new';
}
