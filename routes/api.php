<?php

use App\EntityData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'document'
], function () {

    // TODO: это временный контроллер - удалить после миграции
    Route::apiResource('entity-migrate', 'EntityMigrateController')
        ->parameter('entity-migrate', 'entity')
        ->only(['update']);

    Route::apiResource('/big/entity-migrate', 'BigEntityMigrateController')
        ->parameter('entity-migrate', 'entity')
        ->only(['update']);

    Route::apiResource('entity-copy', 'EntityCopyController')
        ->parameter('entity-copy', 'entity')
        ->only(['update']);

    Route::apiResource('entity', 'EntityController')
        ->except(['destroy']);

    Route::group(['middleware' => 'auth.api'], function() {
        Route::apiResource('fields', 'FieldsController')
            ->except(['destroy']);
        Route::apiResource('generate', 'GenerateController')
            ->parameter('generate', 'documentId')
            ->only(['show']);
    });

    Route::apiResource('npa', 'NpaController')
        ->only(['index', 'show']);

    Route::apiResource('npa', 'NpaController')
        ->except(['index', 'show'])
        ->middleware('auth.api');

    Route::apiResource('npa-link', 'NpaLinkController')
        ->only(['index', 'show']);

    Route::apiResource('npa-link', 'NpaLinkController')
        ->except(['index', 'show', 'destroy'])
        ->middleware('auth.api');

    Route::apiResource('search', 'SearchController')
        ->only(['index']);

    Route::get('rights', function(){
        return response()->json([
            'canSee' => ['constructor', 'editor', 'template']
        ]);
    });

    Route::apiResource('groups', 'GroupsController')->only(['index', 'show']);
    Route::apiResource('groups', 'GroupsController')
        ->except(['index', 'show', 'destroy'])
        ->middleware('auth.api');
    Route::apiResource('user-group', 'UserGroupsController')->except(['destroy']);
    Route::apiResource('generate-description', 'GenerateDescriptionController')
        ->parameter('generate-description', 'contentId')
        ->only(['show']);
    
    Route::get('runtest/{id}',
        function ($id) {
            if ($id == 0) {
                return $linkables = DB::table('npa_linkables')->get();
            }
            $linkables = DB::table('npa_linkables')
                ->where('npa_linkable_id', $id)
                ->get();

            $npa = \App\NpaLink::query()->whereIn('id', $linkables->pluck('npa_link_id')->toArray())->get();

            return [
                'linkables' => $linkables,
                'npa'       => $npa
            ];

        if (request('textAlignCount')) {
            return EntityData::where('payload->text->ru', 'like', '%text-align:justify;%')->count();
        }

        if (request('textAlign')) {
            $data = EntityData::where('payload->text->ru', 'like', '%text-align:justify;%')
                ->take(200)
                ->get();

            foreach ($data as $datum) {
                $payload = $datum->payload;
                foreach ($payload['text'] as $locale => $line) {
                    $payload['text'][$locale] = preg_replace('/text\-align\:justify\;/', '', $line, -1, $count);
                }

                $datum->payload = $payload;
                $datum->save();
            }

            return $data->count();
//            return $data->pluck('payload')->toArray();
        }

        return 'check';
    });
});
