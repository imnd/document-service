<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Filters\Field\GroupTitleFilter;
use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class GroupsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Group::class);
    }
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $group = QueryBuilder::for(Group::class)
            ->allowedFilters([
                  Filter::custom('title', GroupTitleFilter::class)
            ]);
        
        return GroupResource::collection($group->paginate());
    }
    
    /**
     * @param StoreFieldRequest $request
     * @return GroupResource
     */
    public function store(StoreGroupRequest $request)
    {
        $group = new Group($request->all());
        $group->user_id = (new AuthUserService())->getId();
        $group->save();

        $group->fields()->attach($request->get('fields'));

        return new GroupResource($group);
    }
    
    /**
     * @param Group $group
     * @return GroupResource
     */
    public function show(Group $group)
    {
        return new GroupResource($group);
    }
    
    /**
     * @param UpdateGroupRequest $request
     * @param Group $group
     * @return GroupResource
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->except('user_id'));

        $group->fields()->sync($request->get('fields'));

        return new GroupResource($group);
    }
}
