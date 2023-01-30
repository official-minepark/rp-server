<?php

namespace minepark\infrastructure\stores;

use http\Exception\RuntimeException;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use pocketmine\player\Player;

abstract class DataStore implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    private array $store = [];

    protected function exists(string $key): bool
    {
        return array_key_exists($key, $this->store);
    }

    protected function getAll(): array
    {
        return $this->store;
    }

    protected function get(string $key): mixed
    {
        return $this->store[$key] ?? null;
    }

    protected function set(string $key, mixed $value): void
    {
        $this->store[$key] = $value;
    }

    protected function remove(string $key): bool
    {
        if (!$this->exists($key)) {
            return false;
        }

        unset($this->store[$key]);
        return true;
    }
}