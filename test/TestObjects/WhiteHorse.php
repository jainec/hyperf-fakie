<?php

namespace HyperfTest\TestObjects;

class WhiteHorse extends Horse
{
    protected Dog $dog;

    public function toArray(): array
    {
        return [
            'dog' => $this->dog,
            'name' => $this->name,
            'height' => $this->height,
        ];
    }
}