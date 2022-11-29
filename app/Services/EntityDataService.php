<?php

namespace App\Services;

use App\EntityData;
use App\Exceptions\JsonDiffException;
use Illuminate\Database\Eloquent\Builder;
use Swaggest\JsonDiff\Exception;
use Swaggest\JsonDiff\JsonPatch;

class EntityDataService
{
    protected $entityData;
    
    public function __construct(EntityData $entityData)
    {
        $this->entityData = $entityData;
    }
    
    /**
     * @param null $userId
     * @return int
     */
    public function lastVersion($userId = null)
    {
        return (int) EntityData::when($userId, function ($query) use ($userId) { /* @var Builder $query */
                $query->where('user_id', $userId);
            })
            ->when(! $userId, function ($query) { /* @var Builder $query */
                $query->whereNull('user_id');
            })
            ->where('entity_id', $this->entityData->entity_id)
            ->max('version');
    }
    
    /**
     * @param null $userId
     * @return int
     */
    public function firstUserVersion($userId = null)
    {
        if ($userId) {
            return (int) EntityData::where('user_id', $userId)
                    ->where('entity_id', $this->entityData->entity_id)
                    ->min('version');
        }

        return (int) EntityData::whereNull('user_id')
            ->where('entity_id', $this->entityData->entity_id)
            ->max('version');
    }
    
    /**
     * @return array
     */
    public function getVersions()
    {
        return (new EntityService($this->entityData->entity))->getVersions($this->entityData);
    }
    
    /**
     * @param $version
     * @param null $userId
     * @return mixed
     */
    public function findVersion($version, $userId = null)
    {
        return EntityData::when($userId, function ($query) use ($userId) { /* @var Builder $query */
                $query->where('user_id', $userId);
            })
            ->when(! $userId, function ($query) { /* @var Builder $query */
                $query->whereNull('user_id');
            })
            ->where('version', $version)
            ->where('entity_id', $this->entityData->entity_id)
            ->first();
    }
    
    /**
     * @return mixed
     */
    public function wrappedPayload()
    {
        return json_decode(json_encode($this->entityData->payload));
    }
    
    /**
     * @return mixed
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function getPayload()
    {
        $entityData = $this->entityData;
        if ($entityData->payload) return $this->wrappedPayload();
        
        $versions = EntityData::where('entity_id', $entityData->entity_id)
            ->where('version', '>=', $entityData->version)
            ->when($entityData->user_id, function ($query) use ($entityData){ /* @var Builder $query */
                $query->where('user_id', $entityData->user_id);
            })
            ->when(! $entityData->user_id, function ($query){ /* @var Builder $query */
                $query->whereNull('user_id');
            })
            ->orderBy('version', 'desc')
            ->get();
    
        $result = (new self($versions->first()))->wrappedPayload();
        foreach ($versions as $version) {
            if (is_null($version->diff)) continue;
            try {
                JsonPatch::import($version->diff)->apply($result);
            } catch (Exception $e) {
                $exceptionMessage = 'JsonDiff Error: ID= ' . $version->id . ', EntityID=' . $version->entity_id . ', UserID=' . $version->user_id;
                throw new JsonDiffException($exceptionMessage);
            }
        }
        
        return $result;
    }
    
    /**
     * @return mixed|null
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function getText()
    {
        $payload = $this->getPayload();
        if (
               is_null($payload)
            || ! isset($payload->text)
        ) return null;
        
        return $payload->text;
    }
}
