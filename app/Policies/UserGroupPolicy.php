<?php

namespace App\Policies;

use App\User;
use App\UserGroup;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthUserService();
    }

    use HandlesAuthorization;

    public function view(?User $user, UserGroup $userGroup)
    {
        if ($this->authService->checkAuth()) {
            if ((new AuthAbilityService())->userHasAbility('document-user-group-view'))
                return true;

            if ($userGroup->user_id == $this->authService->getId() || $userGroup->user_id == null)
                return true;
        }

        app()->make('validate.uuid');

        return (in_array($userGroup->user_id, [request()->get('uuid', null), null]));
    }

    public function create(?User $user)
    {
        if (!$this->authService->checkAuth())
            app()->make('validate.uuid', ['required' => true]);

        return true;
    }

    public function update(?User $user, UserGroup $userGroup)
    {
        if ($this->authService->checkAuth()) {
            if ((new AuthAbilityService)->userHasAbility('document-user-group-update'))
                return true;

            if ($userGroup->user_id == $this->authService->getId())
                return true;
        }

        app()->make('validate.uuid', ['required' => true]);

        return ($userGroup->user_id == request()->get('uuid'));
    }
}
