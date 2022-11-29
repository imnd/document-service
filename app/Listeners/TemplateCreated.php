<?php

namespace App\Listeners;

use App\Events\DocumentCreated;

use App\Entity;
use App\EntityData;

class TemplateCreated
{
    /**
     * Handle the event.
     *
     * @param  DocumentCreated  $event
     * @return void
     */
    public function handle(DocumentCreated $event)
    {
        $eventData = $event->data;
        if (
               $eventData->version === 1
            && $eventData->entity->type === config('entities.types.document')
        ) {
            $entityId = $eventData->entity_id;
            $entityDataId = $eventData->id;

            $entityFind = Entity::find($entityId);
            $newEntityFind = $entityFind->replicate();
            $newEntityFind->type = 'template';
            $newEntityFind->save();

            $entityDataFind = EntityData::find($entityDataId);

            $newEntityDataFind  = $entityDataFind->replicate();
            $newEntityDataFind->entity_id = $newEntityFind->id;
            $newEntityDataFind->save();

            $doc = EntityData::find($entityDataId);
            $doc->payload = array_merge($doc->payload, ['template_id' =>$newEntityDataFind->entity_id ]);
            $doc->update();

            $docOld = EntityData::find($newEntityDataFind->id);
            $docOld->payload = array_merge($docOld->payload, ['document_id' =>$doc->entity_id ]);
            $docOld->update();
        }
    }
}
