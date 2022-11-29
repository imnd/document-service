<?php

namespace App\Listeners;

use App\Events\DocumentCreated;

use Dogovor24\Queue\Jobs\Explorer\CreateExplorerFileJob;

class CreateFileInExplorerListener
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
            $eventData->version === 1 &&
            $eventData->entity->type === config('entities.types.document') &&
            preg_match('/^[0-9]+$/iu', $eventData->user_id)
        ) {
            CreateExplorerFileJob::dispatch(
                $eventData->entity->id,
                $eventData->user_id,
                $eventData->payload['title'][app()->getLocale()] ?? $eventData->payload['title']['ru'] ?? 'noname',
                'd24',
                $eventData->created_at
            );
        }
    }
}

