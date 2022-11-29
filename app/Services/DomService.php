<?php

namespace App\Services;

class DomService
{
    private $dom;

    /**
     * DomService constructor.
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $this->dom->encoding = 'utf-8';
    }

    /**
     * Remove tags from HTML
     * @param array $tagNames
     */
    public function removeTags(array $tagNames)
    {
        $tagsToRemove = [];

        foreach ($tagNames as $tagName) {
            $tags = $this->dom->getElementsByTagName($tagName);
            foreach ($tags as $tag) {
                $tagsToRemove[] = $tag;
            }
        }

        foreach ($tagsToRemove as $tagToRemove){
            while ($tagToRemove->hasChildNodes()) {
                $child = $tagToRemove->removeChild($tagToRemove->firstChild);
                $tagToRemove->parentNode->insertBefore($child, $tagToRemove);
            }
            $tagToRemove->parentNode->removeChild($tagToRemove);
        }
    }

    /**
     * Remove tags with content from HTML
     * @param array $tagNames
     */
    public function removeTagsWithContent(array $tagNames)
    {
        $tagsToRemove = [];

        foreach ($tagNames as $tagName) {
            $tags = $this->dom->getElementsByTagName($tagName);
            foreach ($tags as $tag) {
                $tagsToRemove[] = $tag;
            }
        }

        foreach ($tagsToRemove as $tagToRemove)
            $tagToRemove->parentNode->removeChild($tagToRemove);
    }

    /**
     * Remove nested tag
     * @param string $tag
     */
    public function removeNestedTag(string $tag)
    {
        $xpath = new \DOMXPath($this->dom);

        $tagsFound = $this->dom->getElementsByTagName($tag);
        foreach ($tagsFound as $tagFound) {
            $nestedTags = $xpath->query("descendant::$tag", $tagFound);
            foreach ($nestedTags as $nestedTag) {
                while ($nestedTag->hasChildNodes()) {
                    $child = $nestedTag->removeChild($nestedTag->firstChild);
                    $nestedTag->parentNode->insertBefore($child, $nestedTag);
                }
                $nestedTag->parentNode->removeChild($nestedTag);
            }
        }
    }

    /**
     * Check tag existence
     * @param string $tagName
     * @return bool
     */
    public function tagExists(string $tagName)
    {
        $tagNodes = $this->dom->getElementsByTagName($tagName);
        if ($tagNodes->length > 0)
            return true;

        return false;
    }

    /**
     * Return HTML
     * @param bool $withoutBody
     * @return string
     */
    public function getHtml(bool $withoutBody = false): string
    {
        $this->dom->normalizeDocument();
        return (!$withoutBody ? '<body>' : '') . $this->dom->saveHTML($this->dom->documentElement) . (!$withoutBody ? '</body>' : '');
    }
}
