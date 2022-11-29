<?php

namespace App\Services;

use App\Services\Description\DescriptionFactory;

class DescriptionService
{
    protected $description;

    public function __construct(array $data)
    {
        $this->description = (new DescriptionFactory($data))->build();
    }

    public function getHtml()
    {
        return $this->description->toHtml();
    }
}
