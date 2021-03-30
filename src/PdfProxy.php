<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Psr\Container\ContainerInterface as Container;
use RuntimeException;

/**
 * @see \Pollen\Pdf\PdfProxyInterface
 */
trait PdfProxy
{
    /**
     * Instance du gestionnaire de PDF.
     * @var PdfProxyInterface|null
     */
    private $pdf;

    /**
     * Instance du gestionnaire de PDF.
     *
     * @return PdfInterface
     */
    public function pdf(): PdfInterface
    {
        if ($this->pdf === null) {
            $container = method_exists($this, 'getContainer') ? $this->getContainer() : null;

            if ($container instanceof Container && $container->has(PdfInterface::class)) {
                $this->pdf = $container->get(PdfInterface::class);
            } else {
                try {
                    $this->pdf = Pdf::getInstance();
                } catch(RuntimeException $e) {
                    $this->pdf = new Pdf();
                }
            }
        }

        return $this->pdf;
    }

    /**
     * Définition du gestionnaire de PDF.
     *
     * @param PdfInterface $pdf
     *
     * @return void
     */
    public function setPdf(PdfInterface $pdf): void
    {
        $this->pdf = $pdf;
    }
}