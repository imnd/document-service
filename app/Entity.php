<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Elastic\EntityConfigurator;
use Dogovor24\Authorization\Contracts\IsSystemRequest;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use ScoutElastic\Searchable;

class Entity extends Model
{
    use Searchable;

    /** @const Типы */
    const TYPE_CONSTRUCTOR = 'constructor';
    const TYPE_DOCUMENT = 'document';
    const TYPE_MATRIX = 'matrix';
    const TYPE_TEMPLATE = 'template';
    const TYPE_TREE = 'tree';

    protected $indexConfigurator = EntityConfigurator::class;

    protected $mapping = [
        'properties' => [
            'text_ru' => [
                'type' => 'text',
                'analyzer' => 'autocomplete_analyzer',
                'search_analyzer' => 'autocomplete_search'
            ]
        ]
    ];

    protected $fillable = [
        'type',
        'main_id',
    ];

    protected $searchRules = [];


    public function toSearchableArray()
    {
        $result = [
            'type' => $this->type,
        ];
        switch ($this->type) {
            case self::TYPE_MATRIX:
            case self::TYPE_TREE: {
                if ($this->masterData) {
                    $data = $this->masterData->payload;
                    foreach ($data['text'] as $locale => $text) {
                        $result["text_$locale"] = $text;
                    }
                }
            } break;
        }
        return $result;
    }

    public function masterData()
    {
        return $this->hasOne(EntityData::class)->whereNull('user_id')->latest();
    }

    public function userData()
    {
        return $this->hasOne(EntityData::class)->latest();
    }

    public function branchData()
    {
        return $this->hasOne(EntityData::class)->orderBy('version');
    }

    public function currentData()
    {
        return $this->hasOne(EntityData::class);
    }

    public function data()
    {
        return $this->hasMany(EntityData::class);
    }

    public function main()
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @param Entity $mainEntity
     * @return bool
     */
    public function setMain(Entity $mainEntity) : bool
    {
        if ($this->type !== $mainEntity->type)
            return false;
        
        $main = (new self($mainEntity))->getMain();
        $this->main()->associate($main);
        
        return (bool) $this->save();
    }

    /**
     * @return Entity
     */
    public function getMain() : Entity
    {
        if (is_null($entity = $this->main))
            return $this;
        
        $mainIds = [];
        do {
            $entity = $entity->getMainSingle();
            if (in_array($entity->id, $mainIds))
                break;
            
            $mainIds[] = $entity->id;
        } while (!is_null($entity->main));
        
        return $entity;
    }

    /**
     * @return Entity
     */
    protected function getMainSingle() : Entity
    {
        return $this->main ?? $this;
    }

    public function scopeAccessible($query, $uuid = null)
    {
        $authService =  new AuthUserService();
        if ($authService->checkAuth() && !(new AuthAbilityService())->userHasAbility('document-entity-view')) {
            $query->whereHas('data', function ($query) use ($authService) {
                $query
                    ->where('user_id', $authService->getId())
                    ->orWhereNull('user_id');
            })
            ->orWhereDoesntHave('data');
        }

        if (!resolve(IsSystemRequest::class) && !$authService->checkAuth()) {
            $query->whereHas('data', function ($query) use ($authService, $uuid) {
                $query
                    ->where('user_id', $uuid)
                    ->orWhereNull('user_id');
            })
            ->orWhereDoesntHave('data');
        }

        return $query;
    }
}
