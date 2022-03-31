<?php

namespace HyperfTest\TestObjects;

abstract class DTO
{
    protected float $height;

    public function fromArray(array $args): static
    {
        foreach ($args as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

}