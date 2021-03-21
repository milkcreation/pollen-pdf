<?php

declare(strict_types=1);

namespace Pollen\Pdf\Drivers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Pollen\Pdf\AbstractPdfDriver;
use Pollen\Pdf\PdfDriverInterface;

class DompdfDriver extends AbstractPdfDriver
{
    /**
     * @inheritDoc
     */
    public function generate(): PdfDriverInterface
    {
        $html = call_user_func($this->renderer);

        $this->generator()->loadHtml($html);
        $this->generator()->render();

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return Dompdf
     */
    public function generator(): object
    {
        if ($this->generator === null) {
            $this->generator = new Dompdf();
        }

        return $this->generator;
    }

    /**
     * @inheritDoc
     */
    public function output(): string
    {
        $this->generate();

        return $this->generator()->output();
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): PdfDriverInterface
    {
        if ($options = $config['options'] ?? []) {
            $this->generator()->setOptions(new Options($options));
        }

        if ($basePath = $config['base_path'] ?? '') {
            $this->generator()->setBasePath($basePath);
        }

        if (isset($params['size']) || isset($config['orientation'])) {
            $size = $config['size'] ?? 'A4';
            $orientation = $config['orientation'] ?? 'portrait';
            $this->generator()->setPaper($size, $orientation);
        }

        return $this;
    }
}