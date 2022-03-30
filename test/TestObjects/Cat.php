<?php

namespace HyperfTest\TestObjects;

class Cat
{
    public function __construct(
        private string $name,
        private House $house,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'house' => $this->house->toArray(),
        ];
    }
}