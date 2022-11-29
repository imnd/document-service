<?php

namespace App\Http\Controllers;

use App\Http\Filters\Npa\NpaLinkTitleFilter;
use App\Http\Filters\Npa\NpaLinkPayloadFilter;
use App\Http\Requests\StoreNpaLinkRequest;
use App\Http\Requests\UpdateNpaLinkRequest;
use App\Http\Resources\NpaLinkCollection;
use App\Http\Resources\NpaLinkResource;
use App\NpaLink;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class NpaLinkController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(NpaLink::class);
    }
    /**
     * @return NpaLinkCollection
     */
    public function index()
    {
        $npaLiks = QueryBuilder::for(NpaLink::class)
            ->allowedFilters([
                Filter::exact('id'),
                Filter::custom('title', NpaLinkTitleFilter::class),

                Filter::custom('part',               NpaLinkPayloadFilter::class),
                Filter::custom('section',            NpaLinkPayloadFilter::class),
                Filter::custom('section_paragraph',  NpaLinkPayloadFilter::class),
                Filter::custom('section_subsection', NpaLinkPayloadFilter::class),
                Filter::custom('chapter',            NpaLinkPayloadFilter::class),
                Filter::custom('chapter_paragraph',  NpaLinkPayloadFilter::class),
                Filter::custom('chapter_subsection', NpaLinkPayloadFilter::class),
                Filter::custom('article',            NpaLinkPayloadFilter::class),
                Filter::custom('point',              NpaLinkPayloadFilter::class),
                Filter::custom('subpoint',           NpaLinkPayloadFilter::class),
                Filter::custom('indent',             NpaLinkPayloadFilter::class),
                Filter::custom('piece',              NpaLinkPayloadFilter::class),
                Filter::custom('piece_indent',       NpaLinkPayloadFilter::class),
            ]);
        
        return new NpaLinkCollection($npaLiks->paginate());
    }
    
    /**
     * @param StoreNpaLinkRequest $request
     * @return NpaLinkResource
     */
    public function store(StoreNpaLinkRequest $request)
    {
//        $npaLink = NpaLink::create($request->all([
//            'npa_id',
//            'link',
//            'payload',
//        ]));
        
        $npaLink = new NpaLink();
        $npaLink->npa_id  = $request->get('npa_id');
        $npaLink->link    = $request->get('link');
        $npaLink->payload = $request->get('payload');
        $npaLink->save();
        
        return new NpaLinkResource($npaLink);
    }
    
    /**
     * @param NpaLink $npaLink
     * @return NpaLinkResource
     */
    public function show(NpaLink $npaLink)
    {
        return new NpaLinkResource($npaLink);
    }
    
    /**
     * @param UpdateNpaLinkRequest $request
     * @param NpaLink $npaLink
     * @return NpaLinkResource
     */
    public function update(UpdateNpaLinkRequest $request, NpaLink $npaLink)
    {
//        $npaLink->npa_id  = $request->get('npa_id');
        $npaLink->link    = $request->get('link');
        $npaLink->payload = $request->get('payload');
        $npaLink->save();
        
        return new NpaLinkResource($npaLink);
    }
}
