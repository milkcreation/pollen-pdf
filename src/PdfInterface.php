<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;

interface PdfInterface extends ConfigBagAwareTraitInterface, ContainerProxyInterface
{
}
