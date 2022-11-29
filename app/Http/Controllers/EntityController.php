<?php

namespace App\Http\Controllers;

use
    App\Entity,
    App\Contracts\EntityResourceContract,
    App\Contracts\ShowEntityRequestContract,
    App\Contracts\StoreEntityRequestContract,
    App\Contracts\UpdateEntityRequestContract,
    App\Contracts\ValidatePayloadStructureContract,
    App\Http\Filters\EntityData\TypeFilter,
    App\Http\Filters\EntityData\UserIdFilter,
    App\Http\Filters\EntityData\VersionFilter,
    App\Http\Requests\IndexEntityRequest,
    App\Http\Resources\EntityDataResource,
    App\Http\Resources\EntityResource,
    App\Services\EntityService,
    Dogovor24\Authorization\Contracts\IsSystemRequest,
    Dogovor24\Authorization\Services\AuthAbilityService,
    Dogovor24\Authorization\Services\AuthRoleService,
    Dogovor24\Authorization\Services\AuthUserService,
    Illuminate\Support\Facades\Redis,
    Ramsey\Uuid\Uuid,
    Spatie\QueryBuilder\Filter,
    Spatie\QueryBuilder\QueryBuilder
;

class EntityController extends Controller
{
    public function __construct()
    {
        if (!resolve(IsSystemRequest::class)) {
            $this->authorizeResource(Entity::class);
        }
    }

    /**
     * @param IndexEntityRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexEntityRequest $request)
    {
        $entities = QueryBuilder::for(Entity::class)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::custom('version', VersionFilter::class),
                Filter::custom('user_id', UserIdFilter::class),
                Filter::custom('type',    TypeFilter::class)
            ])
            ->defaultSort('-updated_at')
            ->allowedSorts('created_at', 'updated_at')
            ->with('masterData')
            ->currentDataMacro(request('filter') ?: []);

        if (request('filter.user_id')) {
            $entities
                ->with('userData')
                ->branchDataMacro(request('filter.user_id'));
        }
        $entities->accessible($request->get('uuid'));

        return EntityResource::collection(
            $entities->paginate()
        );
    }

    /**
     * @param StoreEntityRequestContract $request
     * @param ValidatePayloadStructureContract $parsePayload
     * @return EntityDataResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function store(
        StoreEntityRequestContract $request,
        ValidatePayloadStructureContract $parsePayload
    )
    {
        $payload = $request->get('new');
        $entityType = $request->get('type');
        if ($request->has('copy_id')) {
            $originEntity = Entity::where('id', $request->get('copy_id'))
                ->currentDataMacro([])
                ->first();

            if ($currentData = $originEntity->currentData) {
                $payload = $currentData->payload;
            }
            $entityType = $originEntity->type;
        }
        $entity = new Entity;
        $entity->type = $entityType;
        $entity->save();
        $entity->searchable();

        // TODO: только админ может переназначать главные синонимы у элемента
        if (
               $request->has('main_id')
            && $entity->id != $request->get('main_id')
            && (new AuthUserService())->checkAuth()
            && (new AuthRoleService())->userHasRole('superadmin')
        ) {
            $mainEntity = (Entity::find($request->get('main_id')))->getMain();
            if (!is_null($mainEntity)) {
                $entity->setMain($mainEntity);
            }
        }
        return new EntityDataResource(
            (new EntityService($entity))->addData(
                $parsePayload->parse($payload),
                $request->get('uuid'),
                $request->get('is_lawyer') ?? false
            )
        );
    }

    /**
     * @param ShowEntityRequestContract $request
     * @param Entity $entity
     * @return \Illuminate\Foundation\Application|mixed
     */
    public function show(ShowEntityRequestContract $request, Entity $entity)
    {
        $userSrv = new AuthUserService;
        $userId  = $userSrv->checkAuth() ? $userSrv->getId() : request()->get('uuid');
        $filters = array_merge(
            ['token_user_id' =>
                (
                       $entity->type === config('entities.types.constructor')
                    && $userSrv->checkAuth()
                    && (
                           (new AuthAbilityService())->userHasAbility('constructor-lawyer-permission')
                        || (new AuthAbilityService())->userHasAbility('constructor-lawyer-content-permission')
                    )
                ) ? null : $userId],
            request('filter') ?: []
        );

        $entity = Entity::with([
                'masterData'
            ])
            ->where('id', $entity->id)
            ->currentDataMacro($filters);

        if (request('filter.user_id')) {
            $entity
                ->with('userData')
                ->branchDataMacro(request('filter.user_id'));
        }
        $entity = $entity->first();

        return app(EntityResourceContract::class, compact('entity'));
    }

    /**
     * @param UpdateEntityRequestContract $request
     * @param ValidatePayloadStructureContract $parsePayload
     * @param Entity $entity
     * @return EntityDataResource
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function update(
        UpdateEntityRequestContract $request,
        ValidatePayloadStructureContract $parsePayload,
        Entity $entity
    )
    {
        // TODO Add user id to lock key
        $lockHash = Uuid::uuid4()->toString();
        // Lock
        $redisKey = "working_with_entity_lock:{$entity->id}";
        $redisLockKey = "$redisKey:$lockHash";

        Redis::set($redisLockKey, 'locked');
        Redis::expire($redisLockKey, 10);
        Redis::rPush($redisKey, $lockHash);
        Redis::expire($redisKey, 30);

        $index = 0;
        while (($redisLockHash = Redis::lRange($redisKey, 0, -1)[$index] ?? null) !== $lockHash) {
            $redisExpiredLockKey = "$redisKey:$redisLockHash";
            if (
                   !is_null($redisLockHash)
                && empty(Redis::get($redisExpiredLockKey))
            ) {
                $index++;
            } else {
                $index = 0;
            }
            usleep(100000);
        }

        Redis::expire($redisLockKey, 10);
        Redis::expire($redisKey, 30);

        app()->terminating(function () use ($redisKey, $redisLockKey) {
            Redis::lPop($redisKey);
            Redis::expire($redisKey, 30);
            Redis::del($redisLockKey);
        });

        // TODO: только админ может переназначать главные синонимы у элемента
        if (
               $request->has('main_id')
            && (new AuthUserService())->checkAuth()
            && (new AuthRoleService())->userHasRole('superadmin')
        ) {
            $mainEntity = Entity::find($request->get('main_id'))->getMain();
            if (!is_null($mainEntity)) {
                $entity->setMain($mainEntity);
            }
        }

        return new EntityDataResource(
            (new EntityService($entity))->addData(
                $parsePayload->parse($request->get('payload')),
                $request->get('uuid'),
                $request->get('is_lawyer') ?? false
            )
        );
    }
}
