<?php

namespace Tests\Feature;

use Tests\TestCase,
    App\Entity,
    Tests\Traits\EntityTrait
;

class EntityControllerTest extends TestCase
{
    use EntityTrait;
    
    /**
     * @test
     * @return void
     */
    public function checkIndex()
    {
        $this->createEntities(Entity::TYPE_CONSTRUCTOR);

        $this
            ->setMethod('GET')
            ->setRoute('index', []);

        $data = $this->getRequestResult();
        for ($i = 0; $i < $this->modelsNum; $i++) {
            $this->checkEntityResource($data[$i]);
        }
    }

    /**
     * @test
     * @return void
     */
    public function checkShow()
    {
        $entity = $this->createEntity(Entity::TYPE_DOCUMENT);
        $entityData = $this->createEntityData($entity);

        $this
            ->setMethod('GET')
            ->setRoute();

        $data = $this->getRequestResult([
            'id' => $entity->id,
            'uuid' => $entityData->user_id,
        ]);
        $this->checkEntityResource($data);
    }

    /**
     * @test
     * @return void
     */
    public function checkStore()
    {
        $this
            ->setMethod('POST')
            ->setRoute();

        $this->checkRequestStatus([
            'type' => Entity::TYPE_DOCUMENT,
            'new' => $this->getEntityDataPayload(true),
            'uuid' => Uuid::uuid4()->toString(),
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function checkUpdate()
    {
        $entity = $this->createEntity(Entity::TYPE_DOCUMENT);
        $entityData = $this->createEntityData($entity);

        $this
            ->setMethod('PATCH')
            ->setRoute();

        $this->checkRequestStatus([
            'id' => $entity->id,
            'uuid' => $entityData->user_id,
            'type' => Entity::TYPE_DOCUMENT,
            'payload' => $this->getEntityDataPayload(true, true),
        ], 201 /*неправильный код если чо вашпе то*/);
    }
}
