<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Support\ProxyResolver;
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
            try {
                $this->pdf = Pdf::getInstance();
            } catch (RuntimeException $e) {
                $this->pdf = ProxyResolver::getInstance(
                    PdfInterface::class,
                    Pdf::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
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