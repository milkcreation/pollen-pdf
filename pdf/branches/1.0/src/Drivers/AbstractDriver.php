<?php

declare(strict_types=1);

namespace Pollen\Pdf\Drivers;

use StdClass;
use Pollen\Pdf\Controller\PdfControllerInterface;

abstract class AbstractDriver implements DriverInterface
{
    /**
     * Instance du controleur HTTP associé.
     * @var PdfControllerInterface
     */
    protected $controller;

    /**
     * Instance du pilote de génération de PDF.
     * @var mixed
     */
    protected $generator;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->output();
    }

    /**
     * @inheritDoc
     */
    public function generate(): DriverInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function generator(): object
    {
        return new StdClass();
    }

    /**
     * @inheritDoc
     */
    public function output(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $params): DriverInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setController(PdfControllerInterface $controller): DriverInterface
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function stream()
    {
        $string = $this->output();
        $stream = fopen('php://memory', 'rb+');
        fwrite($stream, $string);
        rewind($stream);

        return $stream;
    }
}