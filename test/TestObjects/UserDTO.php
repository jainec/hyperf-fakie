<?php

namespace HyperfTest\TestObjects;

class UserDTO extends DTO
{
    public string $name;

    protected int $age;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
            'height' => $this->height,
        ];
    }
}