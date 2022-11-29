<?php
/**
 * Created by PhpStorm.
 * User: Arman
 * Date: 14.11.2018
 * Time: 14:27
 */

namespace App\Services;


use App\EntityData;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class MatrixService
{
    private $matrix;
    private $userId;

    public function __construct(array $matrix, $userId = null)
    {
        $this->matrix = $matrix;
        $this->userId = $userId;
    }

    /**
     * @param array $options
     * @param bool $withChildren
     * @return array
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function build(array $options, bool $withChildren = true) : array
    {
        $result = [
            'id'        => $this->matrix['id'],
            'type'      => $this->matrix['type'],
            'is_number' => $this->matrix['is_number'] ?? false,
        ];

        $result['text'] = $this->getText();

        if (
               $withChildren
            && isset($this->matrix['children'])
            && count($this->matrix['children']) > 0
        ) {
            $children = [];

            foreach ($this->matrix['children'] as $child) {
                $childService = new self($child, $this->userId);
                if (!$childService->evaluateExpression($options))
                    continue;
                $children[] = $childService->build($options);
            }

            $result['children'] = $children;
        }

        return $result;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function evaluateExpression(array $options) : bool
    {
        if (!isset($this->matrix['dependencies']) || !isset($this->matrix['dependencies']['expression'])) return true;

        $expression = preg_replace(
            '/\$\{([^\}]+)\}/',
            '(\'$1\' in array)',
            $this->matrix['dependencies']['expression']
        );

        return (new ExpressionLanguage())->evaluate(
            $expression,
            [
                'array' => $options
            ]
        );
    }

    /**
     * @return mixed|null
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function getText()
    {
        $matrixData = EntityData::where('entity_id', $this->matrix['id'])
            ->where('version', $this->matrix['version'])
            ->when($this->matrix['is_user'] ?? false, function($q) {
                $q->where('user_id', $this->userId);
            })
            ->when(!($this->matrix['is_user'] ?? false), function($q) {
                $q->whereNull('user_id');
            })
            ->first();

        if (empty($matrixData))
            return null;
        return (new EntityDataService($matrixData))->getText();
    }
}
