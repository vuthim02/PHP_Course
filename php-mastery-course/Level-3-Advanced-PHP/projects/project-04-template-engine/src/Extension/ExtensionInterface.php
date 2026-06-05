<?php

declare(strict_types=1);

namespace TemplateEngine\Extension;

interface ExtensionInterface
{
    public function getFilters(): array;
    public function getFunctions(): array;
}
