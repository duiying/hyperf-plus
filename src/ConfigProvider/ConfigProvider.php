<?php

declare(strict_types=1);

namespace HyperfPlus\ConfigProvider;

use HyperfPlus\Command\ModuleGenerator;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
                ModuleGenerator::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
