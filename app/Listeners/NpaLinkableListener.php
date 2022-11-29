<?php

namespace App\Listeners;

use App\Entity;
use App\EntityData;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class NpaLinkableListener implements ShouldQueue
{
    private $entityData;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->entityData = EntityData::find($event->entityId);

        if ($this->entityData && $this->entityData->entity->type != 'tree' && $this->entityData->entity->type != 'matrix') {

            $entityIds = [];
            $npaIds = [];

            if ($this->entityData && !empty($this->entityData->payload['trees']))
                foreach ($this->entityData->payload['trees'] as $tree)
                    $entityIds = $this->getEntityIds($tree, $entityIds);

            if (!empty($this->entityData->payload['matrixes']))
                foreach ($this->entityData->payload['matrixes'] as $matrix)
                    $entityIds = $this->getEntityIds($matrix, $entityIds);

            $entities = Entity::whereIn('id', $entityIds)
                ->whereHas('data', function ($query) {
                    $query
                        ->whereNull('user_id')
                        ->where('version', function ($sub) {
                            $sub
                                ->from('entity_datas as en_d')
                                ->whereNull('user_id')
                                ->selectRaw('MAX(en_d.version)')
                                ->whereRaw('en_d.entity_id = entities.id')
                            ;
                        })
                        ->whereNotNull('payload->npas')
                    ;
                })
                ->get()
            ;

            foreach ($entities as $entity) {
                $npaIds = array_merge($npaIds, $entity->data()->whereNull('user_id')->orderBy('version', 'DESC')->first()->payload['npas']);
            }

            if (!empty($this->entityData->payload['trees_data']))
                foreach ($this->entityData->payload['trees_data'] as $tree)
                    if ($tree && !empty($tree['payload']['npas']))
                        $npaIds = array_merge($npaIds, array_map('intval', $tree['payload']['npas']));

            if (!empty($this->entityData->payload['tree_npas']))
                $npaIds = array_merge($npaIds, array_column($this->entityData->payload['tree_npas'], 'id'));


            if (!empty($this->entityData->payload['matrixes_data']))
                foreach ($this->entityData->payload['matrixes_data'] as $matrix)
                    if ($matrix && !empty($matrix->payload['npas']))
                        $npaIds = array_merge($npaIds, array_map('intval', $matrix->payload['npas']));

            if (!empty($this->entityData->payload['matrix_npas']))
                $npaIds = array_merge($npaIds, array_column($this->entityData->payload['matrix_npas'], 'id'));

            $this->clearNpaLinkable();

            $this->storeNpaLinkable(array_unique($npaIds));
        }
    }

    public function getEntityIds(array $data, array $entityIds = []) : array
    {
        if (!in_array($data['id'], $entityIds))   $entityIds[] = $data['id'];

        $children = isset($data['children']) ? $data['children'] : [];

        foreach ($children as $child) {
            $entityIds = $this->getEntityIds($child, $entityIds);
        }

        if (!empty($data['dependencies'])) {

            $matrixes = empty($data['dependencies']['affects_matrixes']) ? [] : $data['dependencies']['affects_matrixes'];
            foreach ($matrixes as $matrix) {
                if (!in_array($matrix, $entityIds)) $entityIds[] = $matrix;
            }

            $trees = empty($data['dependencies']['affects_trees']) ? [] : $data['dependencies']['affects_trees'];
            foreach ($trees as $tree) {
                if (!in_array($tree, $entityIds))   $entityIds[] = $tree;
            }
        }
        return $entityIds;
    }

    public function clearNpaLinkable()
    {
        DB::table('npa_linkables')
            ->where('npa_linkable_id', $this->entityData->entity_id)
            ->whereIn('npa_linkable_type', ['entity', $this->entityData->entity->type])
            ->delete();
    }

    public function storeNpaLinkable(array $npaIds)
    {
        foreach ($npaIds as $npaId)
            DB::table('npa_linkables')->insert([
                'npa_link_id' => $npaId,
                'npa_linkable_id' => $this->entityData->entity_id,
                'npa_linkable_type' => $this->entityData->entity->type
            ]);
    }
}
