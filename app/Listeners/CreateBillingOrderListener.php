<?php

namespace App\Listeners;

use App\Events\DocumentCreated;

use Dogovor24\Authorization\Services\AuthUserService;
use Dogovor24\Queue\Jobs\Billing\CreateProductOrderJob;

class CreateBillingOrderListener
{
    /**
     * Handle the event.
     *
     * @param  DocumentCreated $event
     * @return void
     */
    public function handle(DocumentCreated $event)
    {
        $eventData = $event->data;
        if (
               $eventData->version === 1
            && $eventData->entity->type === config('entities.types.document')
            && (new AuthUserService)->checkAuth()
        ) {
            $title = (isset($eventData->payload['title']) && isset($eventData->payload['title']['ru'])) ?
                $eventData->payload['title']['ru'] : null;

            CreateProductOrderJob::dispatch($eventData->user_id, $eventData->entity->id, $title);
        }
    }
}
