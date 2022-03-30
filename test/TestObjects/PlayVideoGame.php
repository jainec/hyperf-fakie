<?php

namespace HyperfTest\TestObjects;

class PlayVideoGame
{
    public function __construct(
        private string $name,
        private Hobie $hobie,
    )
    {
    }

}