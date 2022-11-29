<?php

namespace App\Providers;

use App\Contracts\EntityResourceContract;
use App\Contracts\ShowEntityRequestContract;
use App\Contracts\StoreEntityRequestContract;
use App\Contracts\UpdateEntityRequestContract;
use App\Contracts\ValidatePayloadStructureContract;
use App\Entity;
use App\Services\BillingService;
use App\Services\Document\DocumentParamsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('currentDataMacro', function (array $filter) { /* @var Builder $this */
            return $this->with([
                'currentData' => function($data) use ($filter) { /* @var Builder $data */
                    $data
                        ->when(isset($filter['version']), function (Builder $builder) use ($filter){
                            $builder->where('version', $filter['version']);
                        })
                        ->when(!isset($filter['version']), function (Builder $builder) use ($filter){
                            $builder->latest('version');
                        })
                    ;

                    $data
                        ->when(isset($filter['user_id']), function (Builder $builder) use ($filter){
                            $builder
                                ->where(function($q) use($filter) {
                                    $q
                                        ->where('user_id', $filter['user_id'])
                                        ->orWhereNull('user_id');
                                });
                        })
                        ->when(!isset($filter['user_id']), function (Builder $builder) use ($filter){
                            $builder
                                ->where(function($q) use($filter) {
                                    $q
                                        ->where('user_id', $filter['token_user_id'] ?? null)
                                        ->orWhereNull('user_id');
                                })
                            ;
                        })
                    ;

                    $data
                        ->distinct()
                        ->orderBy('entity_id', 'desc')
                        ->orderBy('version', 'desc')
                    ;

                    return $data;
                },
            ]);
        });
        
        Builder::macro('branchDataMacro', function ($userId) {
            /* @var Builder $this */
            return $this->with([
                'branchData' => function($data) use ($userId) {
                    /* @var Builder $data */
                    return $data->where('user_id', $userId);
                }
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StoreEntityRequestContract::class, function() {
            $request = config('entities.namespaces.requests') . 'Store' . studly_case(request('type')) . 'Request';
            if (!class_exists($request)) {
                abort(422);
            }
            return $this->app->make($request);
        });

        $this->app->bind(UpdateEntityRequestContract::class, function() {
            // TODO Replace request type to entity type
            $request = config('entities.namespaces.requests') . 'Update' . studly_case(request('type')) . 'Request';
            
            if (!class_exists($request)) {
                abort(422);
            }
            return $this->app->make($request);
        });

        $this->app->bind(ShowEntityRequestContract::class, function() {
            $request = config('entities.namespaces.requests') . 'Show' . studly_case(request()->route('entity')->type) . 'Request';

            if (!class_exists($request)) {
                $request = config('entities.namespaces.requests') . 'ShowRequest';
            }
            return $this->app->make($request);
        });
        
        $this->app->bind(EntityResourceContract::class, function($app, $params) {
            $entity = $params['entity'];
            if (!$entity instanceof Entity) {
                abort(422);
            }
            $resource = config('entities.namespaces.resources') . studly_case($entity->type) .'Resource';
            
            if (!class_exists($resource)) {
                abort(422);
            }
            return new $resource($entity);
        });

        $this->app->bind(ValidatePayloadStructureContract::class, function() {
            $entity = config('entities.namespaces.parses') . studly_case(request('type')) . 'PayloadStructureValidation';
            if (!class_exists($entity))
                abort(422);

            return $this->app->make($entity);
        });

        $this->app->singleton('validate.uuid', function($app, $params) {
            request()->validate([
                'uuid' => (isset($params['required']) ? 'required|' : null) . 'uuid',
            ]);
        });

        $this->app->singleton('document.params', function() {
            $locale = in_array(request('document.locale'), config('documents.locales')) ? request('document.locale') : config('documents.locales.ru');
            $format = in_array(request('document.format'), config('documents.formats')) ? request('document.format') : config('documents.formats.html');
            
            return new DocumentParamsService($locale, $format);
        });

        $this->app->singleton(BillingService::class, function() {
            return new BillingService();
        });
    }
}
