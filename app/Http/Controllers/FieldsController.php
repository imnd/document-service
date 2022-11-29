<?php

namespace App\Http\Controllers;

use App\Field;
use App\Http\Filters\Field\FieldTitleFilter;
use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Http\Resources\FieldResource;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class FieldsController extends Controller
{
    protected $authService;

    public function __construct()
    {
        $this->authorizeResource(Field::class);
        $this->authService = new AuthUserService();
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $fields = QueryBuilder::for(Field::class)
            ->allowedFilters([
                Filter::custom('title', FieldTitleFilter::class)
            ]);

        if (!(new AuthAbilityService)->userHasAbility('document-field-view')) {
            $fields
                ->where('user_id', $this->authService->getId())
                ->orWhereNull('user_id');
        }

        return FieldResource::collection($fields->paginate());
    }
    
    /**
     * @param StoreFieldRequest $request
     * @return FieldResource
     */
    public function store(StoreFieldRequest $request)
    {
        $field = new Field;

        $field->title = $request->get('title');
        $field->type  = $request->get('type');
        
        if ($request->has('placeholder'))
            $field->placeholder = $request->get('placeholder');
        if ($request->has('description'))
            $field->description = $request->get('description');
        if ($request->has('options'))
            $field->options = $request->get('options');
        $field->user_id = null;

        if (!($request->get('is_lawyer') && (new AuthAbilityService())->userHasAbility('document-field-create-lawyer')))
            $field->user_id = $this->authService->getId();
        
        $field->save();
        
        return new FieldResource($field);
    }

    /**
     * @param Field $field
     * @return FieldResource
     */
    public function show(Field $field)
    {
        return new FieldResource($field);
    }

    /**
     * @param UpdateFieldRequest $request
     * @param Field $field
     * @return FieldResource
     */
    public function update(UpdateFieldRequest $request, Field $field)
    {
        $field->title = $request->get('title');
        $field->type  = $request->get('type');

        if ($request->has('placeholder')) {
            $field->placeholder = $request->get('placeholder');
        }
        if ($request->has('description')) {
            $field->description = $request->get('description');
        }
        if ($request->has('options')) {
            $field->options = $request->get('options');
        }
        $field->save();

        return new FieldResource($field);
    }
}
