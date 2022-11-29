<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Http\Requests\GenerateRequest;
use App\Services\DocumentService;
use App\Services\FieldService;
use App\Services\MatrixService;

class GenerateWithTreesController extends Controller
{
    /**
     * @param GenerateRequest $request
     * @param int $documentId
     * @return mixed
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function show(GenerateRequest $request, int $documentId)
    {
        $document = Entity::where('id', $documentId)
            ->currentDataMacro(request('filter') ?: [])
            ->first();
        
        $data = $document ? $document->currentData : null;
        if (
            is_null($data)
            || is_null($data->payload)
            || !isset($data->payload['matrixes'])
        ) abort(422);
        
        $matrixes = $data->payload['matrixes'];
        $options  = $request->get('options');
        $rawData  = [];
        
        foreach ($matrixes as $matrix) {
            $matrixService = new MatrixService($matrix);
            if (!$matrixService->evaluateExpression($options)) continue;
            $rawData[] = $matrixService->build($options);
        }
        
        $docHtml = (new DocumentService($rawData))->getHtml();
        $fields  = $request->get('fields');
        
        if (!empty($fields)) $docHtml = (new FieldService())->fillFields($fields, $docHtml);
        
        return $docHtml;
    }
}
