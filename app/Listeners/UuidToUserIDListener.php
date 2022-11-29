<?php

namespace App\Listeners;

use App\Entity;
use App\EntityData;
use App\UserGroup;
use Dogovor24\Queue\Jobs\Billing\CreateProductOrderJob;

class UuidToUserIDListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (empty($event->uuid)) return;
        $unauthorizedDocuments = Entity::where('type', config('entities.types.document'))
            ->whereHas('data', function ($data) use ($event) {
                $data->where('user_id', $event->uuid);
            })
            ->get()
        ;

        EntityData::where('user_id', $event->uuid)->update(['user_id' => $event->user_id]);
        UserGroup::where('user_id', $event->uuid)->update(['user_id' => $event->user_id]);

        foreach($unauthorizedDocuments as $unauthorizedDocument) {
            $data = $unauthorizedDocument->data()->whereNotNull('payload->title')->first();
            CreateProductOrderJob::dispatch(
                $event->user_id,
                $unauthorizedDocument->id,
                $data ? $data->payload['title'][app()->getLocale()] : null
            );
        }
    }
}
