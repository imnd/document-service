<?php
namespace App\Services\Description;

abstract class DescriptionElement implements DescriptionBlock
{

    protected $text;
    protected $children = [];

    public function build(array $data)
    {
        $this->text = $data['text'];

        if (isset($data['children'])) {
            foreach ($data['children'] as $item) {
                $this->children[] = (new DescriptionFactory($item))->build();
            }
        }
        
        return $this;
    }
    
    public function getText()
    {
        $locale = app('document.params')->getLocale();
        return $this->text->$locale;
    }
}