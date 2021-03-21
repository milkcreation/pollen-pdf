<?php

declare(strict_types=1);

namespace Pollen\Pdf\Partial;

use Pollen\Partial\PartialDriverInterface;
use Pollen\Pdf\PdfProxyInterface;

interface PdfViewerDriverInterface extends PartialDriverInterface, PdfProxyInterface
{
}
