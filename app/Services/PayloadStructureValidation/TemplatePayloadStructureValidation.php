<?php

namespace App\Services\PayloadStructureValidation;

class TemplatePayloadStructureValidation extends ConstructorPayloadStructureValidation
{

    protected $document = [


        'matrixes' =>[
            'id',
            'version',
            'user_id',
            'is_user',
            'type',
            'main_id',
            'is_number',
            'children' => [],
        ],

    ];

    public function __construct()
    {
        if(request()->get('is_lawyer'))
            unset($this->document['matrixes'][array_search('is_user', $this->document['matrixes'])]);
    }


    public function parse(array $data): array
    {

        $data['matrixes'] = $this->createStructure($data, 'matrixes', 'matrixes');
        return $data;

    }

}
