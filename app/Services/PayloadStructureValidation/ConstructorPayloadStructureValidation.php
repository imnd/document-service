<?php

namespace App\Services\PayloadStructureValidation;

use App\Contracts\ValidatePayloadStructureContract;

class ConstructorPayloadStructureValidation implements ValidatePayloadStructureContract
{
    protected $document = [
        'trees' => [
            'id',
            'version',
            'user_id',
            //'is_user',
            'type',
            'dependencies' => [
                'expression',
                'affects_trees',
                'affects_matrixes',
                'depends',
            ],
            'children' => [],
        ],
        'matrixes' => [
            'id',
            'version',
            'user_id',
            //'is_user',
            'type',
            'main_id',
            'is_number',
            'children' => [],
            'dependencies' => [
                'expression',
                'depends',
            ],
        ],
    ];

    public function __construct()
    {
        //if(request()->get('is_lawyer'))
        //    unset(
        //        $this->document['trees'][array_search('is_user', $this->document['trees'])],
        //        $this->document['matrixes'][array_search('is_user', $this->document['matrixes'])]
        //    );
    }

    public function parse(array $data): array
    {
        if (isset($data['trees']))
            $data['trees'] = $this->createStructure($data);
        if (isset($data['matrixes']))
            $data['matrixes'] = $this->createStructure($data, 'matrixes', 'matrixes');
        return $data;
    }


    public function createStructure($data, $dataName = 'trees', $dataKey = 'trees')
    {
        $result = [];
        if (empty($data[$dataKey])) return $result;
        foreach ($data[$dataKey] as $index => $itemData){
            $tempData = [];
            foreach ($this->document[$dataName] as $key => $value) {
                if (is_array($value)) {
                    switch ($key) {
                        case 'children' : {
                            $tempData[$key] = $this->createStructure($itemData, $dataName, $key);
                        } break;
                        case 'dependencies' : {
                            foreach ($value as $dependencyKey) {
                                $tempData[$key][$dependencyKey] = $itemData[$key][$dependencyKey];
                            }
                        } break;
                    }
                } else {
                    if (isset($itemData[$value]))
                        $tempData[$value] = $itemData[$value];
                }
            }
            $result[$index] = $tempData;
        }
        return $result;
    }
}
