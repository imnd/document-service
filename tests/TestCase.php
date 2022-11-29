<?php

namespace Tests;

use
    Illuminate\Foundation\Testing\TestCase as BaseTestCase,
    Illuminate\Foundation\Testing\WithFaker,
    Illuminate\Foundation\Testing\RefreshDatabase,
    Illuminate\Support\Facades\DB,
    ReflectionClass,
    Tests\Traits\AttachJwtToken,
    Tests\Traits\EntityTrait
;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase,
        CreatesApplication,
        WithFaker,
        AttachJwtToken
    ;

    /**
     * Сколько моделей создать для тестирования списка
     * @var int
     */
    protected $modelsNum = 5;
    /**
     * Аутентифицированный пользователь
     * @var \App\User
     */
    protected $user;
    /**
     * HTTP метод
     * @var string
     */
    protected $method;
    /**
     * Какой контроллер тестируем
     * @var string
     */
    protected $controller;
    /**
     * Какой путь тестируем
     * @var string
     */
    protected $route;
    /**
     * Ожидаемый код HTTP статуса
     * @var integer
     */
    protected $expectedStatus;

    /**
     * https://en.wikipedia.org/wiki/Representational_state_transfer
     */
    /*protected*/ const METHODS_ACTIONS = [
        'POST' => 'store',
        'GET' => 'show',
        'PATCH' => 'update',
        'DELETE' => 'destroy',
    ];

    /**
     * https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */
    /*protected*/ const METHODS_RESP_CODES = [
        'GET' => 200,
        'POST' => 201,
        'DELETE' => 202,
        'PATCH' => 204,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        // накатываем БД
        $sql = file_get_contents(__DIR__ . '../../database/migrations/unit_test.sql');
        DB::connection()->getPdo()->exec($sql);

        $this->user = factory('App\User')->create();
        if (is_null($this->controller)) {
            $reflect = new ReflectionClass($this);
            $controllerName = str_replace('ControllerTest', '', $reflect->getShortName());
            $controllerNameArr = preg_split('/(?=[A-Z])/', $controllerName);
            array_shift($controllerNameArr);
            $this->controller = strtolower(implode('-', $controllerNameArr));
        }
        if (!is_null($this->method)) {
            $this->expectedStatus = self::METHODS_RESP_CODES[$this->method];
        }
    }

    /**
     * @param $method string
     * @return TestCase
     */
    protected function setMethod($method)
    {
        $this->method = $method;
        $this->expectedStatus = self::METHODS_RESP_CODES[$this->method];
        return $this;
    }

    /**
     * @param $action string
     * @param $params array
     * @return TestCase
     */
    protected function setRoute($action = null, $params = null)
    {
        if (is_null($params)) {
            $params = 1;
        }
        if (!is_array($params)) {
            $params = [
                strtolower($this->controller) => $params
            ];
        }
        $this->route = route($this->getRoute($action), $params);
        return $this;
    }

    /**
     * @param $action string
     * @return string
     */
    protected function getRoute(string $action = null)
    {
        if (is_null($action)) {
            $action = self::METHODS_ACTIONS[$this->method];
        }
        return (empty($this->controller) ? '' : strtolower($this->controller) . '.') . $action;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createModel($modelName, array $data = [])
    {
        return factory("App\\$modelName")->create($data);
    }

    /**
     * @return [\Illuminate\Database\Eloquent\Model]
     */
    protected function createModels($modelName, array $data = [])
    {
        $models = [];
        for ($i = 0; $i < $this->modelsNum; $i++) {
            $models[] = $this->createModel($modelName, $data);
        }
        return $models;
    }

    /**
     * @param $params array
     * @param $expectedStatus int
     *
     * @return TestCase
     */
    protected function checkRequestStatus(array $params = [], $expectedStatus = null)
    {
        if (is_null($expectedStatus)) {
            $expectedStatus = $this->expectedStatus;
        }
        $this
            ->actingAs($this->user, 'api')
            ->json($this->method, $this->route, $params)
            ->assertStatus($expectedStatus);

        return $this;
    }

    /**
     * @param $params array
     * @param $expectedStatus int
     *
     * @return TestCase
     */
    protected function getRequestResult(array $params = [], $expectedStatus = null)
    {
        if (is_null($expectedStatus)) {
            $expectedStatus = $this->expectedStatus;
        }
        $response = $this->getRequestResponse($params);
        $response->assertStatus($expectedStatus);
        $result = $response->decodeResponseJson();
        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('error', $result);
        $this->assertNotEmpty($data = $result['data']);
        return $data;
    }

    /**
     * @param $params array
     * @param $expectedStatus int
     *
     * @return TestCase
     */
    protected function getRequestResponse(array $params = [])
    {
        return $this
            ->actingAs($this->user, 'api')
            ->json($this->method, $this->route, $params);
    }
}
