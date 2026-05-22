<?php
declare(strict_types=1);

return [
    \T3ac\Person\Domain\Model\Person::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'firstName'  => ['fieldName' => 'first_name'],
            'lastName'   => ['fieldName' => 'last_name'],
            'area'       => ['fieldName' => 'area'],
            'department' => ['fieldName' => 'department'],
        ],
    ],
];