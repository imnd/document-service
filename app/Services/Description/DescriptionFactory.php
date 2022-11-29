<?php
namespace App\Services\Description;

use App\Services\Description\Elements\Description;

class DescriptionFactory
{

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(): DescriptionBlock
    {
        $type = $this->data['type'] ?? null;
        if (in_array($type, config('entities.matrix_types'))) {
            $descriptionElement = config('descriptions.namespaces.elements') . studly_case($type);
            return (new $descriptionElement())->build($this->data);
        }
        
        return (new Description())->build($this->data);
    }
}