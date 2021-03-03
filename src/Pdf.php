<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;

class Pdf implements PdfInterface
{
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * @param array $config
     * @param Container|null $container
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }
    }
}
