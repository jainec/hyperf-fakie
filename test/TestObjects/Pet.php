<?php

namespace HyperfTest\TestObjects;

class Pet
{
    public function __construct(
        private string $name,
        private int $age,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
        ];
    }
}