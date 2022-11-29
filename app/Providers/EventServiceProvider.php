<?php

namespace App\Providers;

use App\Events\DocumentCreated;
use App\Listeners\CreateBillingOrderListener;
use App\Listeners\CreateFileInExplorerListener;
use App\Listeners\NpaLinkableListener;
use App\Listeners\TemplateCreated;
use App\Listeners\UuidToUserIDListener;
use Dogovor24\Queue\Events\Document\DocumentCreatedEvent;
use Dogovor24\Queue\Events\User\UserAuthorized;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class      => [
            SendEmailVerificationNotification::class,
        ],
        DocumentCreated::class => [
            TemplateCreated::class,
            CreateBillingOrderListener::class,
            CreateFileInExplorerListener::class,
        ],
        UserAuthorized::class  => [
            UuidToUserIDListener::class,
        ],
        DocumentCreatedEvent::class => [
            NpaLinkableListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
