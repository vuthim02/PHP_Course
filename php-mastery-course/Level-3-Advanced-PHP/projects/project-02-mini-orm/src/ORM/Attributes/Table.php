<?php

declare(strict_types=1);

namespace MiniORM\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $schema = null
    ) {}
}
