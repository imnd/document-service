<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Http\Requests\GenerateDescriptionRequest;
use App\Services\DescriptionService;
use App\Services\MatrixService;
use Illuminate\Support\Facades\Cache;

class GenerateDescriptionController extends Controller
{
    /**
     * @param GenerateDescriptionRequest $request
     * @param int $contentId
     * @return mixed
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function show(GenerateDescriptionRequest $request, int $contentId)
    {
        if(!($descHtml = Cache::get('entity.description.'.request()->description_id))){

            $description = Entity::where('id', request()->description_id)
                ->where('type', config('entities.types.description'))
                ->currentDataMacro(request('filter') ?: [])
                ->first();

            $data = $description ? $description->currentData : null;
            if (
                   is_null($data)
                || is_null($data->payload)
                || !isset($data->payload['matrixes'])
            ) abort(422);

            $matrixes = $data->payload['matrixes'];
            $rawData  = [];

            foreach ($matrixes as $matrix) {
                $matrixService = new MatrixService($matrix);
                $rawData[] = $matrixService->build([]);
            }

            $descService = (new DescriptionService($rawData));
            $descHtml = $descService->getHtml();

            Cache::forever('entity.description.'.request()->description_id, $descHtml);
        }

        return response()->json(['data' => ['html' => $descHtml]]);

    }
}
