<?php

declare(strict_types=1);

namespace Pollen\Pdf;

Interface PdfProxyInterface
{
    /**
     * Instance du gestionnaire de PDF.
     *
     * @return PdfInterface
     */
    public function pdf(): PdfInterface;

    /**
     * Définition du gestionnaire de PDF.
     *
     * @param PdfInterface $pdf
     *
     * @return PdfProxy|PdfProxyInterface|static
     */
    public function setPdf(PdfInterface $pdf): self;
}