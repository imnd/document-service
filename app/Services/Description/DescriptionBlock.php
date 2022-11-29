<?php
namespace App\Services\Description;

interface DescriptionBlock
{

    public function toHtml();
    public function build(array $data);
    public function getText();
}