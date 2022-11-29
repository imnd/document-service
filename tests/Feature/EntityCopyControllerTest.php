<?php

namespace Tests\Feature;

use
    App\Entity,
    Tests\TestCase,
    Tests\Traits\EntityTrait
;

class EntityCopyControllerTest extends TestCase
{
    use EntityTrait;
    
    /**
     * @test
     * @return void
     */
    public function main()
    {
        $type = Entity::TYPE_DOCUMENT;
        $entity = $this->createEntity($type);
        $entityData = $this->createEntityData($entity);

        $this
            ->setMethod('PATCH')
            ->setRoute('update', $entity->id);

        $this->checkRequestStatus(compact('type'), 201);
    }
}
