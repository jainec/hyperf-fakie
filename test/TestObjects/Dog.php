<?php

namespace HyperfTest\TestObjects;

class Dog extends DTO
{
    public string $id;

    protected static array $owners = [];

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rules' => self::$owners,
            'height' => $this->height,
        ];
    }
}