<?php

declare(strict_types=1);

namespace HyperfTest\TestObjects;

class Job
{
    private string $role;

    private float $salary;

    public function fromArray(array $args)
    {
        foreach ($args as $key => $arg) {
            $this->{$key} = $arg;
        }
    }

    public function fromParameters(string $role, float $salary): static
    {
        $this->role = $role;
        $this->salary = $salary;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'salary' => $this->salary,
        ];
    }
}
