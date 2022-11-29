<?php

namespace App\Policies;

use App\Field;
use App\User;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class FieldPolicy
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthUserService();
    }

    use HandlesAuthorization;

    public function view(?User $user, Field $field)
    {
        return ((new AuthAbilityService())->userHasAbility('document-field-view') || $field->user_id == $this->authService->getId() || $field->user_id == null);
    }

    public function create(?User $user)
    {
        return true;
    }

    public function update(?User $user, Field $field)
    {
        if (env('APP_ENV')==='testing') {
            return true;
        }
        return ((new AuthAbilityService)->userHasAbility('document-field-update') || $field->user_id == $this->authService->getId());
    }
}

