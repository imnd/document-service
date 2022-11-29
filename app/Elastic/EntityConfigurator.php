<?php

namespace App\Elastic;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class EntityConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = 'entity_index';

    /**
     * @var array
     */
    protected $settings = [
        "analysis" => [
            "analyzer" => [
                "autocomplete_analyzer" => [
                    "tokenizer" => "autocomplete_tokenizer",
                    "filter" => ["lowercase"]
                ],
                "autocomplete_search" => [
                    "tokenizer" => "lowercase"
                ]
            ],
            "tokenizer" => [
                "autocomplete_tokenizer" => [
                    "type" => "edge_ngram",
                    "min_gram" => 2,
                    "max_gram" => 10,
                    "token_chars" => ["letter", "digit"]
                ]
            ]
        ],
    ];
}
