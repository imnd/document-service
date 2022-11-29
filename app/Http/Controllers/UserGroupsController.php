<?php

namespace App\Http\Controllers;

use App\Http\Filters\UserGroups\GroupFieldValueFilter;
use App\Http\Filters\UserGroups\GroupTypeFilter;
use App\Http\Requests\IndexUserGroupRequest;
use App\Http\Requests\StoreUserGroupsRequest;
use App\Http\Requests\UpdateUserGroupsRequest;
use App\Http\Resources\UserFieldValueAllResource;
use App\UserGroup;
use Dogovor24\Authorization\Services\AuthUserService;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;


class UserGroupsController extends Controller
{
    protected $authService;

    public function __construct()
    {
        $this->authorizeResource(UserGroup::class);
        $this->authService = new AuthUserService();
    }

    /**
     * @param IndexUserGroupRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexUserGroupRequest $request)
    {
        $group = QueryBuilder::for(UserGroup::class)
            ->allowedFilters([
                Filter::custom('type', GroupTypeFilter::class),
                Filter::exact('group_id'),
                Filter::custom('value', GroupFieldValueFilter::class),
            ]);

        if ($this->authService->checkAuth()/* && !(new AuthAbilityService())->userHasAbility('document-user-group-view')*/) {
            $group
                ->where('user_id', $this->authService->getId())
                ->orWhereNull('user_id');
        }

        if (!$this->authService->checkAuth()) {
            $group
                ->where('user_id', $request->get('uuid'))
                ->orWhereNull('user_id');
        }

        return UserFieldValueAllResource::collection($group->paginate());
    }
    
    /**
     * @param StoreUserGroupsRequest $request
     * @return UserFieldValueAllResource
     */
    public function store(StoreUserGroupsRequest $request)
    {
        $group = new UserGroup($request->all());
        $group->user_id = $this->authService->checkAuth() ? $this->authService->getId() : $request->get('uuid');
        $group->save();

        $group->fields()->attach($request->get('user_group'));

        return new UserFieldValueAllResource($group);
    }

    /**
     * @param \App\UserGroup $userGroup
     * @return UserFieldValueAllResource
     */
    public function show(UserGroup $userGroup)
    {
        return new UserFieldValueAllResource($userGroup);
    }

    /**
     * @param UpdateUserGroupsRequest $request
     * @param \App\UserGroup $userGroup
     * @return UserFieldValueAllResource
     */
    public function update(UpdateUserGroupsRequest $request, UserGroup $userGroup)
    {
        $userGroup->update($request->except('user_id'));

        $userGroup->fields()->sync($request->get('user_group'));

        return new UserFieldValueAllResource($userGroup);
    }
}
