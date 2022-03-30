<?php

namespace HyperfTest\TestObjects;

class PetOwner
{
    public function __construct(
        private string $name,
        private Pet $pet,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'pet' => $this->pet->toArray(),
        ];
    }
}