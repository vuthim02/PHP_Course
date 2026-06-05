<?php

declare(strict_types=1);

namespace MicroFramework\Container;

class Container
{
    private array $bindings = [];
    private array $instances = [];
    private array $aliases = [];

    public function set(string $id, mixed $value): void
    {
        $this->bindings[$id] = $value;
        unset($this->instances[$id]);
    }

    public function setSingleton(string $id, callable|object $value): void
    {
        if (is_object($value) && !$value instanceof \Closure) {
            $this->instances[$id] = $value;
        } else {
            $this->bindings[$id] = $value;
            $this->instances[$id] = null;
        }
    }

    public function alias(string $alias, string $id): void
    {
        $this->aliases[$alias] = $id;
    }

    public function get(string $id): mixed
    {
        $id = $this->aliases[$id] ?? $id;

        if (array_key_exists($id, $this->instances)) {
            if ($this->instances[$id] === null && isset($this->bindings[$id])) {
                $factory = $this->bindings[$id];
                $this->instances[$id] = $factory($this);
            }
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id])) {
            $binding = $this->bindings[$id];
            if ($binding instanceof \Closure) {
                return $binding($this);
            }
            return $binding;
        }

        return $this->autowire($id);
    }

    public function has(string $id): bool
    {
        $id = $this->aliases[$id] ?? $id;
        return isset($this->bindings[$id]) || isset($this->instances[$id]) || class_exists($id);
    }

    public function make(string $class, array $params = []): object
    {
        return $this->resolveClass($class, $params);
    }

    private function autowire(string $class): object
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("Cannot resolve: $class — class not found and not bound");
        }

        return $this->resolveClass($class);
    }

    private function resolveClass(string $class, array $overrideParams = []): object
    {
        $refClass = new \ReflectionClass($class);
        $constructor = $refClass->getConstructor();

        if ($constructor === null) {
            return $refClass->newInstance();
        }

        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $overrideParams)) {
                $params[] = $overrideParams[$name];
                continue;
            }

            $type = $param->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $typeName = $type->getName();

                if ($this->has($typeName)) {
                    $params[] = $this->get($typeName);
                } elseif (class_exists($typeName)) {
                    $params[] = $this->autowire($typeName);
                } elseif ($param->isDefaultValueAvailable()) {
                    $params[] = $param->getDefaultValue();
                } else {
                    throw new \RuntimeException(
                        "Cannot resolve parameter \${$name} of type {$typeName} for {$class}"
                    );
                }
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } elseif ($type !== null && $type->allowsNull()) {
                $params[] = null;
            } else {
                throw new \RuntimeException(
                    "Cannot resolve parameter \${$name} for {$class}"
                );
            }
        }

        return $refClass->newInstanceArgs($params);
    }
}
