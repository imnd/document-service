<?php namespace App\Services\Document\Elements;

use App\Services\Document\DocumentElement;
use App\Services\Document\DocumentFactory;

class Document extends DocumentElement
{

    public function toHtml()
    {
        $str = '';
        $str.= '<ol>';
        foreach ($this->children as $child) {
            /* @var $child \App\Services\Document\DocumentBlock */
            $str.= $child->toHtml();
        }
        $str.= '</ol>';
        return $str;
    }

    public function build(array $data)
    {
        $this->text = $data['title'] ?? 'Unnamed document';
        if (isset($data)) {
            foreach ($data as $item) {
                $this->children[] = (new DocumentFactory($item))->build();
            }
        }
        return $this;
    }
}