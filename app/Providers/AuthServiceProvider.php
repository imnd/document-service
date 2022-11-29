<?php

namespace App\Providers;

use App\Entity;
use App\Field;
use App\Group;
use App\Npa;
use App\NpaLink;
use App\Policies\EntityPolicy;
use App\Policies\FieldPolicy;
use App\Policies\GroupPolicy;
use App\Policies\NpaLinkPolicy;
use App\Policies\NpaPolicy;
use App\Policies\UserGroupPolicy;
use App\UserGroup;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Entity::class => EntityPolicy::class,
        Field::class => FieldPolicy::class,
        Npa::class => NpaPolicy::class,
        NpaLink::class => NpaLinkPolicy::class,
        Group::class => GroupPolicy::class,
        UserGroup::class => UserGroupPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
