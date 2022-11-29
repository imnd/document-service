<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateRequest;
use App\Services\Document\DocumentService;
use App\Services\FieldService;
use App\Services\MatrixService;
use Dogovor24\Authorization\Services\AuthUserService;

class GenerateController extends Controller
{
    /**
     * @param GenerateRequest $request
     * @return mixed
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function show(GenerateRequest $request, int $documentId)
    {
        $userId = (new AuthUserService)->getId();
        $data = null;
        if ($document = request()->documentEntity) {
            $data = $document->userData()
                ->where('user_id', $userId)
                ->orderBy('version', 'desc')
                ->first();
        }
        if (
               is_null($data)
            || is_null($data->payload)
            || !isset($data->payload['matrixes'])
        ) {
            abort(422);
        }

        $matrixes = $data->payload['matrixes'];
        $rawData = [];
        foreach ($matrixes as $matrix) {
            $matrixService = new MatrixService($matrix, $userId);
//            if (!$matrixService->evaluateExpression($options)) continue;
            $rawData[] = $matrixService->build([]);
        }

        $format = $request->has('format') ? $request->get('format') : 'docx';
        $docService = (new DocumentService($rawData, $format));
        $docHtml = $docService->getHtml();
        if (!empty($fields = $request->get('fields'))) {
            $docHtml = (new FieldService)->fillFields($fields, $docHtml);
        }
        $title = $data->payload['title'][app()->getLocale()] . ".$format" ?? null;
        $barcode = $data->payload['barcode'] ?? null;
        return $docService->download($docHtml, $title, $barcode);
    }
}
