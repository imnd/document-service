<?php namespace App\Services\Document;

abstract class DocumentElement implements DocumentBlock
{

    protected $text;

    protected $isNumber;

    protected $children = [];

    public function build(array $data)
    {
        $this->text     = $data['text'];
        $this->isNumber = $data['is_number'];

        if (isset($data['children'])) {
            foreach ($data['children'] as $item) {
                $this->children[] = (new DocumentFactory($item))->build();
            }
        }

        return $this;
    }

    public function getText()
    {
        $locale = app('document.params')->getLocale();
        if (is_array($this->text)) return $this->text[$locale] ?? '';
        if (is_string($this->text)) return $this->text ?? '';
        return $this->text->$locale ?? '';
    }
}
