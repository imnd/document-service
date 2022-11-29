<?php

namespace App\Http\Resources;

use App\GroupField;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFieldValueAllResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'group_type' => $this->group_type,
            'field_value' => $this->value_fields,
        ];
    }
}
