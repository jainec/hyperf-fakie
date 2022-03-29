<?php

declare(strict_types=1);

namespace HyperfTest;

class User
{
    public function __construct(
        private string $name,
        private int $age,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
        ];
    }
}
