<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 26.11.2018
 * Time: 15:05
 */

namespace App\Services\Document;


class DocumentParamsService
{
    private $locale;
    private $format;
    
    public function __construct(string $locale, string $format)
    {
        $this->locale = $locale;
        $this->format = $format;
    }
    
    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
    
    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}