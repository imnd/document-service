<?php namespace App\Services\Document\Elements;

use App\Services\Document\DocumentElement;

class Header extends DocumentElement
{

    public function toHtml()
    {
        $text = $this->getText();
        $str = '';
        if (!empty($text)) {
            $str = '<span style="font-size: 18px">'.strip_tags($text).'</span>';

            if ($this->isNumber) $str = '<li>'.$str.'</li>';
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
