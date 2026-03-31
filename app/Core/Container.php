<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function singleton(string $id, Closure $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->bindings[$id])) {
            throw new RuntimeException("Service [$id] is not bound.");
        }

        return $this->instances[$id] = ($this->bindings[$id])($this);
    }
}
