<?php

declare(strict_types=1);

namespace MicroFramework\Container;

interface ServiceProviderInterface
{
    public function register(Container $container): void;
}
