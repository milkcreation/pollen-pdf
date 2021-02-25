<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ContainerAwareTrait;
use Psr\Container\ContainerInterface as Container;

class Pdf implements PdfInterface
{
    use ConfigBagAwareTrait;
    use ContainerAwareTrait;

    /**
     * @param array $config
     * @param Container|null $container
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }
    }
}
