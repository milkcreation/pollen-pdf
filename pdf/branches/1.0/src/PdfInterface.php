<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\PartialProxyInterface;

interface PdfInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerProxyInterface,
    PartialProxyInterface
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): PdfInterface;
}
