<?php

declare(strict_types=1);

namespace BMClientBundle\Client;

use BMClientBundle\Client\DependencyInjection\BMClientExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BMClientBundle extends Bundle
{
    public const VERSION = '0.0.1-beta1';

    public function getContainerExtension()
    {
        return new BMClientExtension();
    }
}
