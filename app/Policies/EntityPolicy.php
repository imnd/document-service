<?php

namespace App\Policies;

use App\Entity;
use App\User;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
{
    use HandlesAuthorization;

    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthUserService;
    }

    public function view(?User $user, Entity $entity)
    {
        if ($this->authService->checkAuth()) {
            if (
                   (new AuthAbilityService)->userHasAbility('document-entity-view')
                || $this->entityDataExists($entity, $this->authService->getId())
            ) {
                return true;
            }
        }

        app()->make('validate.uuid');

        return $this->entityDataExists($entity, request()->get('uuid', null));
    }

    public function create(?User $user)
    {
        if (!$this->authService->checkAuth()) {
            app()->make('validate.uuid', ['required' => true]);
        }
        return true;
    }

    public function update(?User $user, Entity $entity)
    {
        if ($this->authService->checkAuth()) {
            if (
                   (new AuthAbilityService)->userHasAbility('document-entity-update')
                || $this->entityDataExists($entity, $this->authService->getId())
            ) {
                return true;
            }
        }

        app()->make('validate.uuid', ['required' => true]);

        return $this->entityDataExists($entity, request()->get('uuid', null));
    }

    /**
     * @param mixed $userId
     * @return boolean
     */
    private function entityDataExists($entity, $userId)
    {
        return $entity
            ->data()
            ->where(function($query) use ($userId) {
                $query
                    ->where('user_id', $userId)
                    ->orWhereNull('user_id');
            })
            ->exists();
    }
}
