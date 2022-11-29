<?php

namespace App\Http\Controllers;

use App\Http\Filters\Npa\NpaPayloadFilter;
use App\Http\Filters\Npa\NpaTitleFilter;
use App\Http\Requests\StoreNpaRequest;
use App\Http\Requests\UpdateNpaRequest;
use App\Http\Resources\NpaCollection;
use App\Http\Resources\NpaResource;
use App\Npa;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class NpaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Npa::class);
    }

    /**
     * @return NpaCollection
     */
    public function index()
    {
        $npas = QueryBuilder::for(Npa::class)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::custom('title', NpaTitleFilter::class),

                Filter::custom('part',               NpaPayloadFilter::class),
                Filter::custom('section',            NpaPayloadFilter::class),
                Filter::custom('section_paragraph',  NpaPayloadFilter::class),
                Filter::custom('section_subsection', NpaPayloadFilter::class),
                Filter::custom('chapter',            NpaPayloadFilter::class),
                Filter::custom('chapter_paragraph',  NpaPayloadFilter::class),
                Filter::custom('chapter_subsection', NpaPayloadFilter::class),
                Filter::custom('article',            NpaPayloadFilter::class),
                Filter::custom('point',              NpaPayloadFilter::class),
                Filter::custom('subpoint',           NpaPayloadFilter::class),
                Filter::custom('indent',             NpaPayloadFilter::class),
                Filter::custom('piece',              NpaPayloadFilter::class),
                Filter::custom('piece_indent',       NpaPayloadFilter::class),
            ]);
    
        return new NpaCollection($npas->paginate());
    }

    /**
     * @param StoreNpaRequest $request
     * @return NpaResource
     */
    public function store(StoreNpaRequest $request)
    {
        $npa = new Npa();

        foreach ($request->get('title') as $locale => $title) {
            $npa->setTranslation('title', $locale, $title);
        }
        $npa->save();

        return new NpaResource($npa);
    }
    
    /**
     * @param Npa $npa
     * @return NpaResource
     */
    public function show(Npa $npa)
    {
        return new NpaResource($npa);
    }
    
    /**
     * @param UpdateNpaRequest $request
     * @param Npa $npa
     * @return NpaResource
     */
    public function update(UpdateNpaRequest $request, Npa $npa)
    {
        if ($request->has('main_id')) $npa->main_id = $request->get('main_id');
        
        foreach ($request->get('title') as $locale => $title) {
            $npa->setTranslation('title', $locale, $title);
        }
        $npa->save();
        
        return new NpaResource($npa);
    }
    
    /**
     * @param Npa $npa
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Npa $npa)
    {
        if (is_null($npa->main_id) || !is_null($npa->main->main_id)) abort(422);
        
        $counter = $npa->links()->update([
            'npa_id' => $npa->main->id
        ]);
        
        $npa->delete();
        
        return response()->json([
            'deleted_at'    => (string) $npa->deleted_at,
            'links_updated' => $counter
        ]);
    }
}
