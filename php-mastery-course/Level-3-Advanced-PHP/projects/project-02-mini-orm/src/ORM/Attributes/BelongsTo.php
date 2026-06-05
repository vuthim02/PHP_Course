<?php

declare(strict_types=1);

namespace MiniORM\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class BelongsTo
{
    public function __construct(
        public readonly string $targetEntity,
        public readonly string $column,
        public readonly string $referencedColumn = 'id'
    ) {}
}
