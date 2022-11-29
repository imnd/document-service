<?php

namespace App\Policies;

use App\NpaLink;
use App\User;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Illuminate\Auth\Access\HandlesAuthorization;

class NpaLinkPolicy
{
    use HandlesAuthorization;

    public function view(?User $user, NpaLink $npaLink)
    {
        return true;
    }

    public function create(?User $user)
    {
        return (new AuthAbilityService)->userHasAbility('document-npa-link-create');
    }

    public function update(?User $user, NpaLink $npaLink)
    {
        return (new AuthAbilityService)->userHasAbility('document-npa-link-update');
    }
}
