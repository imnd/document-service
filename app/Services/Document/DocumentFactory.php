<?php namespace App\Services\Document;

use App\Services\Document\Elements\Document;
use App\Services\Document\Elements\Header;
use App\Services\Document\Elements\Paragraph;

class DocumentFactory
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(): DocumentBlock
    {
        $type = $this->data['type'] ?? null;
        if (in_array($type, config('entities.matrix_types'))) {
            $documentElement = config('documents.namespaces.elements') . studly_case($type);
            /* @var DocumentElement::class $documentElement */
            return (new $documentElement())->build($this->data);
        }
        
        return (new Document)->build($this->data);
    }
}
