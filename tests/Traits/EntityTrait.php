<?php

namespace Tests\Traits;

use
    App\Entity,
    App\Services\EntityService,
    Ramsey\Uuid\Uuid
;

trait EntityTrait
{
    /**
     * @param string type
     * @param array $data
     * @return 
     */
    protected function createEntity($type, array $data = [])
    {
        return $this->createModel('Entity', array_merge($data, compact('type')));
    }

    /**
     * @param string type
     * @param array $data
     * @return 
     */
    protected function createEntities($type, array $data = [])
    {
        return $this->createModels('Entity', array_merge($data, compact('type')));
    }

    /**
     * @param Entity $entity
     * @return 
     */
    protected function createEntityData($entity, $uuid = false)
    {
        if ($uuid===false) {
            $uuid = Uuid::uuid4()->toString();
        }
        return (new EntityService($entity))->addData(
            $this->getEntityDataPayload(),
            $uuid,
            true
        );
        
        return $this->createModel('EntityData', $data);
    }

    /**
     * @param $params array
     * @param $expectedStatus int
     *
     * @return TestCase
     */
    protected function checkRequestResult(array $params = [], $expectedStatus = null)
    {
        $data = $this->getRequestResult($params, $expectedStatus);
        $this->checkEntityResource($data);
    }

    /**
     * @param boolean $addMatrixes
     * @param boolean $addTrees
     * @return array
     */
    protected function getEntityDataPayload($addMatrixes = false, $addTrees = false)
    {
        $matrixes = $trees = [];
        if ($addMatrixes) {
            for ($i=0; $i<5; $i++) {
                $matrixes[] = [
                    'id' => $this->createEntity(Entity::TYPE_MATRIX)->id,
                    'type' => ['header', 'paragraph'][rand(0, 1)],
                    'version' => rand(1, 10),
                    'position' => rand(1, 10),
                    'user_id' => $this->user->id,
                    'is_user' => false,
                    'is_number' => true,
                    'canRender' => true,
                    'children' => [
                        ['id' => $this->createEntity(Entity::TYPE_MATRIX)->id]
                    ],
                ];
            }
        }
        if ($addTrees) {
            for ($i=0; $i<5; $i++) {
                $trees[] = [
                    'id' => $this->createEntity(Entity::TYPE_TREE)->id,
                    'main_id' => null,
                    'branchId' => rand(1, 100),
                    'type' => 'question',
                    'version' => rand(1, 10),
                    'position' => rand(1, 10),
                    'children' => [],
                ];
            }
        }
        return [
            'title' => ['ru' => $this->faker->name],
            'matrixes' => $matrixes,
            'trees' => $trees,
            'template_id' => $this->createEntity(Entity::TYPE_TEMPLATE)->id,
            'constructor_id' => $this->createEntity(Entity::TYPE_CONSTRUCTOR)->id,
            'constructor_version' => rand(1, 10),
        ];
    }

    /**
     * @param array $data
     * @return TestCase
     */
    protected function checkEntityResource(array $data)
    {
        print '<pre>'.print_r($data, true).'</pre>';die;
        $this->assertArrayHasKey('main_id', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('version', $data);

        return $this;
    }
}
