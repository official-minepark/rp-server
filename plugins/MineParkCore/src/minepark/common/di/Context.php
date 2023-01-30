<?php

namespace minepark\common\di;

use AttachableLogger;
use Generator;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonTrait;
use minepark\plugin\MainPlugin;
use pocketmine\Server;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use RuntimeException;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitGenerator\GeneratorUtil;
use SOFe\AwaitGenerator\Mutex;
use SOFe\AwaitStd\AwaitStd;

class Context implements Singleton
{
    use SingletonTrait;

    /** @template T
     * @var array<class-string<T>>, T|Mutex>
     */
    private array $storage = [];

    public function __construct()
    {
        $this->store($this);
    }

    public function store(Singleton|AwaitStd $object): void
    {
        $this->storage[$object::class] = $object;
    }

    /**
     * @template T of Singleton
     * @param class-string<T> $className
     * @param Generator<mixed, mixed, mixed, T> $promise
     * @return Generator<mixed, mixed, mixed, T>
     */
    public function loadOrStoreAsync(string $className, Generator $promise): Generator
    {
        if (!is_subclass_of($className, Singleton::class) and !is_subclass_of($className, FromContext::class)) {
            throw new RuntimeException("$className is not implementing Singleton and FromContext");
        }

        if ($this->dependencyExists($className)) {
            if ($this->storage[$className] instanceof Mutex) {
                yield from $this->storage[$className]->run(GeneratorUtil::empty());
            }

            return $this->storage[$className];
        }

        $mutex = new Mutex();
        $this->storage[$className] = $mutex;
        yield from $mutex->runClosure(function () use ($className, $promise): Generator {
            $std = $this->getOrNull(AwaitStd::class);

            $object = yield from $std->timeout($promise, 20 * 5);

            if ($object === null) {
                throw new RuntimeException("Class $className took more than 5 seconds to initialize");
            }

            $this->store($object);
        });

        return $this->storage[$className];
    }

    public function dependencyExists(string $className): bool
    {
        return isset($this->objectStore[$className]);
    }

    /**
     * @template T of Singleton|AwaitStd
     * @param class-string<T> $className
     * @return T
     */
    public function getOrNull(string $className)
    {
        return $this->storage[$className] ?? null;
    }

    /**
     * @return Generator<mixed, mixed, mixed, list<mixed>>
     */
    public function resolveArgs(ReflectionFunctionAbstract $method, string $invokerClass): Generator
    {
        $parameters = $method->getParameters();

        $futures = [];

        foreach ($parameters as $parameter) {
            $gen = function () use ($parameter): Generator {
                $parameterType = $parameter->getType();

                if (!$parameterType instanceof ReflectionNamedType) {
                    throw new RuntimeException("Parameter $parameterType has to have exact data type");
                }

                $parameterClass = $parameterType->getName();

                if ($parameterClass === Server::class) {
                    return Server::getInstance();
                } else if ($parameterClass === AttachableLogger::class) {
                    return $this->getOrNull(MainPlugin::class)->getLogger();
                } else if ($parameterClass === AwaitStd::class) {
                    return MainPlugin::getAwaitStd($this);
                }

                if (is_subclass_of($parameterClass, Singleton::class)) {
                    return yield from $parameterClass::getInstance($this);
                }

                $object = $this->getOrNull($parameterClass);

                if ($object === null) {
                    throw new RuntimeException("$parameterClass is not stored in Context");
                }

                return $object;
            };

            $futures[] = $gen();
        }

        return yield from Await::all($futures);
    }
}