<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 21.11.2018
 * Time: 11:23
 */

namespace App\Services;


use App\EntityData;

class DescriptionResourceService
{
    /**
     * @param array $array
     * @return array
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function getEntityDataPayloads(array $array, $userId = null)
    {
        $params = $this->getEntityDataParams($array);

        $payloads = [];
        $data     = EntityData::query();
        $data->where(function($data) use ($params, $userId){
            foreach ($params as $entityId => $itemData) {
                $data->orWhere(function ($query) use ($entityId, $itemData, $userId){
                    $query
                        ->where('entity_id', $entityId)
                        ->where('version', $itemData->version)
                        ->when(!($itemData->is_user ?? false), function ($query) {
                            return $query->whereNull('user_id');
                        })
                        ->when($itemData->is_user ?? false, function ($query) use ($userId) {
                            return $query
                                ->when($userId, function ($query) use ($userId) {
                                    return $query->where('user_id', $userId);
                                })
                                ->when(!$userId, function ($query) use ($userId) {
                                    return $query->whereNull('user_id');
                                });
                        })
                    ;
                });
            }
        });
        
        $data = $data->get();
        
        foreach ($data as $item) {
            $payloads[$item->entity_id] = [
                'id'      => $item->entity_id,
                'payload' => (new EntityDataService($item))->getPayload()
            ];
        }
        
        return $payloads;
    }
    
    /**
     * @param array $array
     * @return array
     */
    public function getEntityDataParams(array $array) : array
    {
        $params = [];
        foreach ($array as $item) {
            $params = $params + $this->getEntityDataParamsSingle($item);
        }
        
        return $params;
    }
    
    /**
     * @param $item
     * @return array
     */
    private function getEntityDataParamsSingle($item)
    {
        $params[$item->id] = $item;
        $children          = empty($item->children) ? [] : $this->getEntityDataParams($item->children);
        
        return $params + $children;
    }
}