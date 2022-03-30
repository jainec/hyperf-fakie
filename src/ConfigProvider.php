<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf Fakie.
 *
 * @link     https://github.com/jainec
 * @document https://github.com/jainec/hyperf-fakie/blob/master/README.md
 * @contact  @jaineccs
 * @license  https://github.com/jainec/hyperf-fakie/blob/master/LICENSE
 */
namespace JaineC\Hyperf\Fakie;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'Configuration file to define your own rules to populate objects for tests.',
                    'source' => __DIR__ . '/../publish/fakie.php',
                    'destination' => BASE_PATH . '/config/autoload/fakie.php',
                ],
            ],
        ];
    }
}