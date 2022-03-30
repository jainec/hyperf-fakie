<?php

declare(strict_types=1);

use HyperfTest\TestObjects\BlockHouse;
use JaineC\Hyperf\Fakie\Fakie;

return [
    'rules' => [
        'HyperfTest\TestObjects\User' => [
            'cpf' => '12345678910',
        ],
        'HyperfTest\TestObjects\Cat' => [
            'house' => Fakie::object(BlockHouse::class),
        ],
    ],
];
