<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Pdf\Partial\PdfViewerDriver;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ResourcesAwareTrait;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\Support\Exception\ManagerRuntimeException;
use Psr\Container\ContainerInterface as Container;

class Pdf implements PdfInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ResourcesAwareTrait;
    use ContainerProxy;
    use PartialProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

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

        $this->setResourcesBaseDir(dirname(__DIR__) . '/resources');

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): PdfInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): PdfInterface
    {
        if (!$this->isBooted()) {
            //events()->trigger('pdf.booting', [$this]);

            $this->partial()->register(
                'pdf-viewer',
                $this->containerHas(PdfViewerDriver::class)
                    ? PdfViewerDriver::class : new PdfViewerDriver($this, $this->partial())
            );
            $this->setBooted();
            //events()->trigger('pdf.booted', [$this]);
        }

        return $this;
    }
}
