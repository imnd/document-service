<?php namespace App\Services\Document\Elements;

use App\Services\Document\DocumentElement;
use App\Services\DomService;

class Paragraph extends DocumentElement
{

    public function toHtml()
    {
        $str = $this->getText();
        if (!empty($str)) {
            $domService = new DomService($str);
            if ($this->isNumber && !$domService->tagExists('table')) {
                $str = '<li>'.$str.'</li>';
            }
        }

        if (! empty($this->children)) {
            $str .= '<ol>';
            foreach ($this->children as $child) {
                /* @var $child \App\Services\Document\DocumentBlock */
                $str .= $child->toHtml();
            }
            $str .= '</ol>';
        }
        return $str;
    }
}