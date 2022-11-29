<?php
namespace App\Services\Description\Elements;

use App\Services\Description\DescriptionElement;
use App\Services\Description\DescriptionFactory;

class Description extends DescriptionElement
{

    public function toHtml()
    {
        $str= '';
        foreach ($this->children as $child) {
            $str.= $child->toHtml();
        }
        return $str;
    }

    public function build(array $data)
    {
        if (isset($data)) {
            foreach ($data as $item) {
                $this->children[] = (new DescriptionFactory($item))->build();
            }
        }
        return $this;
    }
}