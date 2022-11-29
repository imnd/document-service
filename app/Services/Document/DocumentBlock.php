<?php

namespace App\Services\Document;

interface DocumentBlock
{

    public function toHtml();

    public function build(array $data);

    public function getText();
}