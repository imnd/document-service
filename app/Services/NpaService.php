<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 20.11.2018
 * Time: 14:39
 */

namespace App\Services;



use App\Http\Resources\NpaLinkDocsResource;
use App\NpaLink;

class NpaService
{
    /**
     * @param array $payload
     * @param string $key
     * @return array
     */
    public function parsePayload(array $payload, string $key = 'text') : array
    {
        if (!isset($payload[$key])) return $payload;
        
        $npas = [];
        foreach ($payload[$key] as $locale => $text) {
            $text_npas = $this->parseText($text);
            
            if (count($text_npas) > 0) $npas = array_merge($npas, $text_npas);
        }
        
        if (count($npas) > 0) $payload['npas'] = array_unique($npas);
    
        return $payload;
    }
    
    /**
     * @param string $string
     * @return array
     */
    public function parseText(string $string) : array
    {
        preg_match_all('/(?<=data\-npa\=[\"\']{1})[\d\,]+/', $string, $matches);
        
        $npas = [];
        foreach ($matches[0] as $match) {
            $npas = array_merge($npas, explode(',', $match));
        }
        
        return array_unique($npas);
    }
    
    /**
     * @param $array
     * @return array
     */
    public function getNpasFromArray($array) : array
    {
        $npas = [];
        foreach ($array as $item) {
            $npas = array_merge($npas, $this->getNpasFromSingleItem($item));
        }
        
        return array_unique($npas);
    }
    
    /**
     * @param $item
     * @return array
     */
    protected function getNpasFromSingleItem($item)
    {
        $npas     = $item->npas ?? [];
        $children = empty($item->children) ? [] : $this->getNpasFromArray($item->children);
        
        return array_merge($npas, $children);
    }
    
    /**
     * @param array $npaIds
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function loadNpaLinkData(array $npaIds)
    {
        $treeNpasRaw = NpaLink::with('npa')
            ->whereIn('id', $npaIds)
            ->get();
        
        return NpaLinkDocsResource::collection($treeNpasRaw);
    }
    
    /**
     * @param array $payloadArray
     * @return array
     */
    public function getNpaIdsFromPayload(array $payloadArray)
    {
        $npaIds = [];
        foreach ($payloadArray as $payload) {
            if (!isset($payload['payload']->npas)) continue;
            $npaIds = array_merge($payload['payload']->npas, $npaIds);
        }
        
        return $npaIds;
    }
}