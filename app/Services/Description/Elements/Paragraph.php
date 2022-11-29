<?php
namespace App\Services\Description\Elements;

use App\Services\Description\DescriptionElement;

class Paragraph extends DescriptionElement
{

    public function toHtml()
    {
        $str = $this->getText();

        if (! empty($this->children)) {
            foreach ($this->children as $child) {
                $str .= $child->toHtml();
            }
        }
        return $str;
    }
}