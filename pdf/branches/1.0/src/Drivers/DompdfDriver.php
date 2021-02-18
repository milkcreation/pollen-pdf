<?php

declare(strict_types=1);

namespace Pollen\Pdf\Drivers;

use Dompdf\Dompdf as BaseDompdf;
use Dompdf\Options;

/**
 * @see https://github.com/dompdf/dompdf/wiki/Usage
 */
class DompdfDriver extends AbstractDriver
{
    /**
     * @inheritDoc
     */
    public function generate(): Adapter
    {
        $html = $this->controller->html();

        $this->generator()->loadHtml($html);
        $this->generator()->render();

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return BaseDompdf
     */
    public function generator(): BaseDompdf
    {
        if ($this->generator === null) {
            $this->generator = new BaseDompdf();
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
    public function setConfig(array $params): DriverInterface
    {
        if ($options = $params['options'] ?? []) {
            $this->generator()->setOptions(new Options($options));
        }

        if ($basePath = $params['base_path'] ?? '') {
            $this->generator()->setBasePath($basePath);
        }

        if (isset($params['size']) || isset($params['orientation'])) {
            $size = $params['size'] ?? 'A4';
            $orientation = $params['orientation'] ?? 'portrait';
            $this->generator()->setPaper($size, $orientation);
        }

        return $this;
    }
}