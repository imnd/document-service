<?php

namespace App\Http\Controllers;

use
    App\Entity,
    App\EntityData,
    App\Http\Requests\Entity\CopyDocumentRequest,
    App\Http\Resources\EntityResource,
    GuzzleHttp\Exception\GuzzleException,
    Exception,
    Dogovor24\Authorization\Services\AuthRequestService,
    Dogovor24\Authorization\Contracts\IsSystemRequest
;

class EntityCopyController extends Controller
{
    public function __construct()
    {
        if (!resolve(IsSystemRequest::class)) {
            $this->authorizeResource(Entity::class);
        }
    }

    /**
     * @param CopyDocumentRequest $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     */
    public function update(CopyDocumentRequest $request, Entity $entity)
    {
        $newEntity = $entity->replicate();
        $newEntity->save();
        $newEntity->searchable();

        $entityDataModels = EntityData::where('entity_id', $entity->id)->get();
        foreach ($entityDataModels as $entityData) {
            $newEntityData = $entityData->replicate();
            $newEntityData->entity_id = $newEntity->id;
            $newEntityData->save();
        }
        try {
            $client = (new AuthRequestService(config('api.billing_url')))->getHttpClient(false, true);
            $client->request(
                'PATCH',
                'product-copy/1',
                [
                    'json' => [
                        'old_entity_id' => $entity->id,
                        'new_entity_id' => $newEntity->id,
                    ]
                ]
            );
        } catch (GuzzleException | Exception $e) {
            return ['error' => print_r($e, true)];
        }
        return EntityResource::collection($newEntity);
    }
}
