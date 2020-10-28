<?php
return [
    'patient' => [
        'type' => 1,
        'description' => 'пациент',
        'ruleName' => 'userRole',
    ],
    'specialist' => [
        'type' => 1,
        'description' => 'специалист',
        'ruleName' => 'userRole',
    ],
    'admin' => [
        'type' => 1,
        'description' => 'администратор',
        'ruleName' => 'userRole',
        'children' => [
            'patient',
            'specialist',
        ],
    ],
];
