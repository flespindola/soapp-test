<?php

return [

    'default' => 'fuzzy',

    'fieldName' => 'relevance',

    'drivers' => [

        'fuzzy' => [
            'class' => 'Monogram\Searchy\SearchDrivers\FuzzySearchDriver',
        ],

        'ufuzzy' => [
            'class' => 'Monogram\Searchy\SearchDrivers\FuzzySearchUnicodeDriver',
        ],

        'simple' => [
            'class' => 'Monogram\Searchy\SearchDrivers\SimpleSearchDriver',
        ],

        'levenshtein' => [
            'class' => 'Monogram\Searchy\SearchDrivers\LevenshteinSearchDriver',
        ],

    ],

];
