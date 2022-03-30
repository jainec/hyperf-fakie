<?php

namespace HyperfTest\TestObjects;

class BlockHouse implements House
{
    public function __construct(
        private float $size,
        private string $color,
    )
    {
    }

    public function toArray(): array{
        return [
            'size' => $this->size,
            'color' => $this->color,
        ];
    }
}