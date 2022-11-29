<?php

namespace App\Http\Controllers;

use App\EntityData;
use Illuminate\Http\Resources\Json\JsonResource;

class EntityDataController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResource
     */
    public function show(int $id)
    {
        $data = EntityData::where('entity_id', $id)
//            ->whereNull('user_id')
            ->orderBy('version', 'desc')
            ->first();

        return new JsonResource($data);
    }
}
