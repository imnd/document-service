<?php

namespace Tests\Feature;

use Tests\TestCase,
    App\Entity,
    Ramsey\Uuid\Uuid;

class FieldsControllerTest extends TestCase
{
    /**
     * @test
     * пока не проходит
     * @return void
     */
    public function checkIndex()
    {
        $fields = $this->createFields();
        $this
            ->setMethod('GET')
            ->setRoute('index', []);

        $data = $this->getRequestResult();
        for ($i = 0; $i < $this->modelsNum; $i++) {
            $this->checkFieldResource($data[$i]);
        }
    }

    /**
     * @test
     * @return void
     */
    public function checkShow()
    {
        $field = $this->createField();
        $this
            ->setMethod('GET')
            ->setRoute();

        $data = $this->getRequestResult([
            'id' => $field->id,
        ]);
        $this->checkFieldResource($data);
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

        $this->checkRequestStatus($this->getFieldData());
    }

    /**
     * @test
     * @return void
     */
    public function checkUpdate()
    {
        $field = $this->createField();

        $this
            ->setMethod('PATCH')
            ->setRoute();

        $this->checkRequestStatus(array_merge($this->getFieldData(), [
            'id' => $field->id,
        ]), 200);
    }

    /**
     * @param string type
     * @return 
     */
    protected function createField(array $data = [])
    {
        return $this->createModel('Field', array_merge([
            'user_id' => $this->user->id,
        ], $data));
    }

    /**
     * @param string type
     * @return 
     */
    protected function createFields(array $data = [])
    {
        return $this->createModels('Field', array_merge([
            'user_id' => $this->user->id,
        ], $data));
    }

    protected function getFieldData()
    {
        return [
            'user_id' => $this->user->id,
            'title' => ['ru' => $this->faker->text],
            'placeholder' => ['ru' => $this->faker->text],
            'description' => ['ru' => $this->faker->text],
            'type' => 'input',
            'options' => [''],
        ];
    }

    /**
     * @param array $data
     */
    protected function checkFieldResource(array $data)
    {
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('placeholder', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('options', $data);

        return $this;
    }
}
