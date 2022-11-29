<?php

namespace App\Services\PayloadStructureValidation;

use App\Contracts\ValidatePayloadStructureContract;
use App\Services\DomService;
use App\Services\FieldService;
use App\Services\NpaService;

class MatrixPayloadStructureValidation implements ValidatePayloadStructureContract
{

    public function parse(array $data): array
    {
        $payload = [];
        foreach (config('documents.locales') as $locale) {
            if (isset($data['text'][$locale])){
                $domService = new DomService($data['text'][$locale]);
                $domService->removeTags(['br', 'figure']);
                $domService->removeTagsWithContent(['svg']);
                $payload['text'][$locale] = $domService->getHtml(true);
            }
        }
        
        $payload = (new NpaService())->parsePayload($payload);
        $payload = (new FieldService())->parsePayload($payload);
        
        if (
            isset($data['type'])
            && in_array($data['type'], config('entities.matrix_payload_types'))
        ) $payload['type'] = $data['type'];
        
        return $payload;
    }
}
