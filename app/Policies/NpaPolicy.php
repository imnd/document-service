<?php

namespace App\Policies;

use App\User;
use App\Npa;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class NpaPolicy
{
    use HandlesAuthorization;

    public function view(?User $user, Npa $npa)
    {
        return true;
    }

    public function create(?User $user)
    {
        return (new AuthAbilityService())->userHasAbility('document-npa-create');
    }

    public function update(?User $user, Npa $npa)
    {
        return (new AuthAbilityService())->userHasAbility('document-npa-update');
    }

    public function delete(?User $user, Npa $npa)
    {
        return (new AuthAbilityService())->userHasAbility('document-npa-delete');
    }

    public function restore(?User $user, Npa $npa)
    {
        return (new AuthAbilityService())->userHasAbility('document-npa-delete');
    }

    public function forceDelete(?User $user, Npa $npa)
    {
        return (new AuthAbilityService())->userHasAbility('document-npa-delete');
    }
}
