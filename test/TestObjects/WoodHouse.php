<?php

namespace HyperfTest\TestObjects;

class WoodHouse
{
    public function __construct(
        private string $name,
        private House $house,
    )
    {
    }
}