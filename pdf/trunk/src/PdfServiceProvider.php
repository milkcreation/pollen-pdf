<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Container\BaseServiceProvider;
use Pollen\Pdf\Drivers\DriverInterface;
use Pollen\Pdf\Drivers\DompdfDriver;

class PdfServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        DriverInterface::class,
        PdfInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(PdfInterface::class, function() {
            return new Pdf([], $this->getContainer());
        });

        $this->getContainer()->share(DriverInterface::class, function() {
            return new DompdfDriver();
        });
    }
}