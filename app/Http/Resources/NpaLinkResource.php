<?php

namespace App\Http\Resources;

use App\Npa;
use Illuminate\Http\Resources\Json\JsonResource;

class NpaLinkResource extends JsonResource
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
            'id'      => $this->id,
            'npa'     => new NpaResource($this->npa),
            'title'   => $this->npa->getTranslations('title'),
            'link'    => $this->link,
            'payload' => $this->payload,
        ];
    }
}
