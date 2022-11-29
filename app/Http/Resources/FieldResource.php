<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
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
            'id'          => $this->id,
            'title'       => $this->getTranslations('title'),
            'placeholder' => $this->getTranslations('placeholder'),
            'description' => $this->getTranslations('description'),
            'type'        => $this->type,
            'options'     => $this->options,
            'user_id'     => $this->user_id,
        ];
    }
}
