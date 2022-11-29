<?php

namespace App\Services;

use App\Entity;
use App\EntityData;
use App\Events\DocumentCreated;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Dogovor24\Queue\Jobs\Document\DocumentCreatedJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Redis;
use Swaggest\JsonDiff\JsonDiff;
use Swaggest\JsonDiff\JsonPatch;

class EntityService
{
    protected $entity;
    
    /**
     * EntityService constructor.
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param string|null $userId
     * @return int
     */
    public function lastVersion(string $userId = null)
    {
        return (int) $this->entity->data()
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when(! $userId, function ($query) {
                $query->whereNull('user_id');
            })
            ->max('version');
    }
    
    /**
     * @param string $userId
     * @return int
     */
    public function firstUserVersion(string $userId) : int
    {
        return (int) $this->entity->data()->where('user_id', $userId)->min('version');
    }
    
    /**
     * @return array
     */
    public function getVersions(EntityData $entityData = null) : array
    {
        $currentEntityData = $entityData ?: $this->entity->currentData;
        
        $versions = [
            'master'  => is_null($this->entity->masterData) ? null : $this->entity->masterData->version, // текущая версия в системе
            'current' => is_null($currentEntityData)        ? null : $currentEntityData->version,        // запрошенная версия
        ];

        if (
               $this->entity->relationLoaded('userData')
            || $currentEntityData && $currentEntityData->user_id
        ) {
            $versions['user']   = is_null($this->entity->userData)   ? null : $this->entity->userData->version;   // запрошенная или текущая версия пользователя
            $versions['branch'] = is_null($this->entity->branchData) ? null : $this->entity->branchData->version; // первая версия пользователя
        }

        return $versions;
    }
    
    /**
     * @param int $versionId
     * @param string|null $userId
     * @return EntityData
     */
    public function findVersion(int $versionId, string $userId = null)
    {
        return $this->entity->data()
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when(! $userId, function ($query) {
                $query->whereNull('user_id');
            })
            ->where('version', $versionId)
            ->first();
    }
    
    /**
     * @param string $userId
     * @return EntityData
     */
    public function makeUserBranch(string $userId)
    {
        $latest = $this->entity->data()
            ->whereNull('user_id')
            ->latest('version')
            ->first();

        if (!$latest)
            return null;
    
        $userBranch = new EntityData();
        $userBranch->version = $latest->version;
        $userBranch->payload = $latest->payload;
        $userBranch->merged  = $latest->version;
        $userBranch->user_id = $userId;
        $userBranch->entity()->associate($this->entity);
        $userBranch->save();
        
        return $userBranch;
    }
    
    /**
     * @param string|null $userId
     * @return EntityData|\Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getLatestData(string $userId = null)
    {
        $latest = $this->entity->data()
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when(! $userId, function ($query) {
                $query->whereNull('user_id');
            })
            ->latest('version')
            ->first();
        
        if ($latest)
            return $latest;

        return $userId ? $this->makeUserBranch($userId) : null;
    }
    
    /**
     * @param EntityData $entityData
     * @return mixed
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function getPayload(EntityData $entityData)
    {
        if ($entityData->payload) return (new EntityDataService($entityData))->wrappedPayload();
        
        $versions = $this->entity->data()
            ->where('entity_id', $entityData->entity_id)
            ->where('version', '>=', $entityData->version)
            ->when($entityData->user_id, function ($query) use ($entityData){ /* @var Builder $query */
                $query->where('user_id', $entityData->user_id);
            })
            ->when(! $entityData->user_id, function ($query){ /* @var Builder $query */
                $query->whereNull('user_id');
            })
            ->orderBy('version', 'desc')
            ->get();

        $result = (new EntityDataService($versions->first()))->wrappedPayload();
        foreach ($versions as $version) {
            if (is_null($version->diff)) continue;
            JsonPatch::import($version->diff)->apply($result);
        }
        return $result;
    }

    /**
     * @param array $payload
     * @param string|null $uuid
     * @param bool $isLawyer
     * @return EntityData
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function addData(array $payload, string $uuid = null, $isLawyer = false)
    {
        $authService = new AuthUserService;
        $authCheck = $authService->checkAuth();
        $userId = $authCheck ? $authService->getId() : $uuid;
        $userId =
               $isLawyer
            && $authCheck
            && (
                (new AuthAbilityService())->userHasAbility('constructor-lawyer-permission', $userId)
                || (
                    // check lawyer-outsource content role
                    (new AuthAbilityService())->userHasAbility('constructor-lawyer-content-permission', $userId) &&
                    (
                        (!in_array($this->entity->type, [config('entities.types.description'), config('entities.types.constructor')]))
                        ||
                        (
                            in_array($this->entity->type, [config('entities.types.description'), config('entities.types.constructor')]) &&
                            (new ContentService($this->entity->id))->getRoles($authService->getId(), true)->count()
                        )
                    )
                )
            )
            ? null : $userId;

        $previousEntityData = $this->getLatestData($userId);

        if ($previousEntityData) {
            $diff  = new JsonDiff($payload, $previousEntityData->payload);
            $patch = $diff->getPatch()->jsonSerialize();
            
            $previousEntityData->payload = null;
            $previousEntityData->diff    = $patch;
            $previousEntityData->save();
        }

        $newData = new EntityData();

        $newData->user_id = $userId;
        $newData->payload = $payload;
        //$newData->version = $previousEntityData ? ++$previousEntityData->version : 1;
        $newData->version = 0;

        $newData->entity()->associate($this->entity);
        $newData->save();

        DB::table($newData->getTable())
            ->where('id', $newData->id)
            ->update([
                'version' => DB::raw('
                    (
                        SELECT COALESCE(MAX(version) + 1, 1) 
                        FROM ' . $newData->getTable() . ' 
                        WHERE entity_id = ' . $newData->entity_id . ' 
                            AND user_id ' . (is_null($newData->user_id) ? 'IS NULL' : "= '{$newData->user_id}'") . '
                    )
                ')
            ])
        ;
        $newData->refresh();

        event(new DocumentCreated($newData));

        if ($userId === null) {
            DocumentCreatedJob::dispatch($newData->id);
        }
        if ($this->entity->type === config('entities.types.description')) {
            Cache::forget('entity.description.' . $this->entity->id);
        }

        return $newData;
    }
}
