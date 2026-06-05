<?php

declare(strict_types=1);

namespace MiniORM\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        public readonly string $name,
        public readonly string $type = 'string',
        public readonly bool $nullable = false,
        public readonly mixed $default = null
    ) {}
}
