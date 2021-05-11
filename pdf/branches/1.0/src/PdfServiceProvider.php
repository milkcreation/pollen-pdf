<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Container\BootableServiceProvider;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Pdf\Drivers\DompdfDriver;
use Pollen\Pdf\Partial\PdfViewerDriver;

class PdfServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        PdfInterface::class,
        PdfDriverInterface::class,
        PdfViewerDriver::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(PdfInterface::class, function() {
            return new Pdf([], $this->getContainer());
        });

        $this->getContainer()->share(PdfDriverInterface::class, function() {
            return new DompdfDriver();
        });

        $this->getContainer()->add(PdfViewerDriver::class, function() {
            return new PdfViewerDriver(
                $this->getContainer()->get(PdfInterface::class),
                $this->getContainer()->get(PartialManagerInterface::class)
            );
        });
    }
}