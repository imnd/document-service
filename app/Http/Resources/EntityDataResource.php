<?php

namespace App\Http\Resources;

use App\Services\EntityDataService;
use App\Services\EntityService;
use Illuminate\Http\Resources\Json\JsonResource;

class EntityDataResource extends JsonResource
{
    protected $entity;
    
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function toArray($request)
    {
        $this->entity = $entity = $this->resource->entity;
        $entityDataService = new EntityDataService($this->resource);

        return [
            'id'         => $entity->id,
            'type'       => $entity->type,
            'main_id'    => $entity->main_id,
            'user_id'    => $this->user_id,
            'payload'    => $entityDataService->getPayload(),
            'version'    => $entityDataService->getVersions(),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('entity.show', ['entity' => $this->entity->id]),
            ],
        ];
    }
}
