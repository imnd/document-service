<?php

return [

    'types' => [
        'tree'        => 'tree',
        'matrix'      => 'matrix',
        'document'    => 'document',
        'template'    => 'template',
        'constructor' => 'constructor',
        'description' => 'description',
    ],

    'tree_types' => [
        'checkbox' => 'checkbox',
        'radio'    => 'radio',
        'question' => 'question',
    ],

    'matrix_types' => [
        'header'    => 'header',
        'paragraph' => 'paragraph',
    ],

    'matrix_payload_types' => [
        'document'    => 'document', // TODO: удалить, когда станет не нужна
        'constructor' => 'constructor',
        'description' => 'description',
        'article'     => 'article',
    ],

    'namespaces' => [
        'requests'  => 'App\Http\Requests\Entity\\',
        'parses'    => 'App\Services\PayloadStructureValidation\\',
        'resources' => 'App\Http\Resources\EntityTypes\\',
    ],

];
