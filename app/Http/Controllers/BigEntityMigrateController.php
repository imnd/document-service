<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Http\Requests\Entity\UpdateMigrateConstructorRequest;
use App\Services\EntityService;

class BigEntityMigrateController extends Controller
{
    /**
     * @param UpdateMigrateConstructorRequest $request
     * @param Entity $entity
     * @return string
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function update(UpdateMigrateConstructorRequest $request, Entity $entity)
    {
        $data = $entity->data()
            ->whereNotNull('payload')
            ->whereNull('user_id')
            ->orderBy('version', 'desc')
            ->first();
        
        $payload     = is_null($data) ? [] : $data->payload;
        $payloadNew  = $request->get('payload');
        $matrixesNew = isset($payloadNew['matrixes']) ? $payloadNew['matrixes'] : [];
        
        if (empty($matrixesNew)) $payload = array_merge($payload, $payloadNew);
        else {
            $matrixes = isset($payload['matrixes']) ? $payload['matrixes'] : [];
            $matrixes = array_merge($matrixes, $matrixesNew);
            
            // TODO: do we need unique array values?
            
            $payload['matrixes'] = $matrixes;
        }
        
        if (isset($payload['trees'])) {
            $payload['trees'] = $this->makeTreeAffects($payload['trees'], $payload['trees'], 'dependencies.affects_trees');
            
            if (isset($payload['matrixes'])) $payload['trees'] = $this->makeTreeAffects($payload['matrixes'], $payload['trees'], 'dependencies.affects_matrixes');
        }
        
        return (new EntityService($entity))->addData($payload, null, true);
    }
    
    
    private function makeTreeAffects(array $source, array $result, string $affects) : array
    {
        $flatResult = [];
        $flatSource = [];
        
        foreach ($source as $key => $item) {
            $this->makeFlatTrees($source, $key, $item, $flatSource);
        }
        
        foreach ($result as $key => $item) {
            $this->makeFlatTrees($result, $key, $item, $flatResult);
        }
    
    
        foreach ($flatSource as $sourceItems) {
    
            foreach ($sourceItems as $sourceItem) {
                $depends = $this->getDepends($sourceItem);
                
                if (empty($depends)) continue;
                
                foreach ($depends as $depend) {
                    foreach ($flatResult[$depend] as $resultKey => $resultItem) {
                        $resultAffect = array_get($flatResult[$depend][$resultKey], $affects);
                        $resultAffect = array_merge($resultAffect, [$sourceItem['id']]);
                        
                        array_set($flatResult[$depend][$resultKey], $affects, $resultAffect);
                    }
                }
            }
        }
        
        
        return $result;
    }
    
    private function makeFlatTrees(array &$start, string $key, array $arr, array &$flat)
    {
        $loc  = &$start;
        foreach (explode('.', $key) as $step) {
            $loc = &$loc[$step];
        }
        
        $flat[$arr['id']][] = &$loc;
        
        if (isset($arr['children'])) {
            foreach ($arr['children'] as $k => $child) {
                $this->makeFlatTrees($start, $key .'.children.'. $k, $child, $flat);
            }
        }
    }
    
    private function getDepends(array $source) : array
    {
        $depends = array_get($source, 'dependencies.depends');
    
        $dependNew = [];
        foreach ($depends as $depend) {
            preg_match('/(?<=\:)(\d+)$/', $depend, $matches);
            if ($matches) $dependNew[] = $matches[0];
        }
        
        return $dependNew;
    }
}
