<?php

namespace App\Services\PayloadStructureValidation;

use App\Contracts\ValidatePayloadStructureContract;
use App\Services\NpaService;

class TreePayloadStructureValidation implements ValidatePayloadStructureContract
{

    public function parse(array $data): array
    {
        $payload = [];
        foreach (config('documents.locales') as $locale) {
            if (isset($data['text'][$locale]))
                $payload['text'][$locale] = preg_replace('~<br([\s/][^>]*)?>~isU', '', $data['text'][$locale]);
        }
        
        $payload = (new NpaService())->parsePayload($payload);
        
        return $payload;
    }
}
