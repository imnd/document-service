<?php

namespace App\Http\Resources\EntityTypes;

use App\Contracts\EntityResourceContract;
use App\Http\Resources\EntityResource;
use App\Services\DocumentResourceService;
use App\Services\EntityService;
use App\Services\FieldService;
use App\Services\NpaService;

class TemplateResource extends EntityResource implements EntityResourceContract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function toArray($request)
    {
        $entityService = new EntityService($this->resource);
        
        $payload = is_null($this->resource->currentData) ? null : $entityService->getPayload($this->resource->currentData);

        if (!is_null($payload)) {
            
            $resourceService = new DocumentResourceService();
            $npaService      = new NpaService();

            if (!empty($payload->matrixes)) {
                $matrixPayloads = $resourceService->getEntityDataPayloads($payload->matrixes, $this->resource->currentData->user_id);
                $matrixNpaIds = $npaService->getNpaIdsFromPayload($matrixPayloads);
                $matrixNpas = $npaService->loadNpaLinkData($matrixNpaIds);

//                $fields = $request->get('fields');
//                if (!empty($fields)) $matrixPayloads = (new FieldService())->fillMatrixSetFields($fields, $matrixPayloads);

                $payload->matrixes_data = $matrixPayloads;
                $payload->matrix_npas   = $matrixNpas;
            }
            
        }
        
        return [
            'id'      => $this->id,
//            'main_id' => $this->main_id,
            'type'    => $this->type,
            
            'created_at' => is_null($this->resource->currentData) ? null : (string) $this->resource->currentData->created_at,
            'updated_at' => is_null($this->resource->currentData) ? null : (string) $this->resource->currentData->updated_at,
            'version'    => $entityService->getVersions(),
            'payload'    => $payload,
        ];
    }
}
