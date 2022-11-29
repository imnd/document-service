<?php

namespace App\Policies;

use App\Entity;
use App\Group;
use App\User;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    public function view(?User $user, Group $group)
    {
        return true;
    }

    public function create(?User $user)
    {
        return (new AuthAbilityService())->userHasAbility('document-group-create');
    }

    public function update(?User $user, Group $group)
    {
        return ((new AuthAbilityService())->userHasAbility('document-group-update') || $group->user_id == (new AuthUserService())->getId());
    }
}
