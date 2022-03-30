<?php

declare(strict_types=1);

namespace HyperfTest\TestObjects;

class User
{
    public function __construct(
        private string $name,
        public int $age,
        protected float $height,
        private array $languages,
        private bool $active,
        private string $cpf,
        private $hobie,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'age' => $this->age,
            'height' => $this->height,
            'languages' => $this->languages,
            'active' => $this->active,
            'cpf' => $this->cpf,
            'hobie' => $this->hobie,
        ];
    }
}
