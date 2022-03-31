<?php

namespace HyperfTest\TestObjects;

class Game
{
    private string $name;

    public function __construct(string $name, float $value)
    {
        $this->name = $name;
        $value = $value*2;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}