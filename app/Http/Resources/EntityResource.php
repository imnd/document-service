<?php

namespace App\Http\Resources;

use App\Services\EntityService;
use Illuminate\Http\Resources\Json\JsonResource;

class EntityResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function toArray($request)
    {
        $entityService = new EntityService($this->resource);
        
        return [
            'id'      => $this->id,
            'main_id' => $this->main_id,
            'type'    => $this->type,
            
            'created_at' => is_null($this->currentData) ? null : (string) $this->currentData->created_at,
            'updated_at' => is_null($this->currentData) ? null : (string) $this->currentData->updated_at,
            'version'    => $entityService->getVersions(),
            'payload'    => is_null($this->currentData) ? null : $entityService->getPayload($this->currentData),
        ];
    }

    public function with($request)
    {
        return [
            'links' => [
                'self' => route('entity.show', ['entity' => $this->id]),
            ],
        ];
    }
}
