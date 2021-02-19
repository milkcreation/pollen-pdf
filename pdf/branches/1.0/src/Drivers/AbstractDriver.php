<?php

declare(strict_types=1);

namespace Pollen\Pdf\Drivers;

use StdClass;

abstract class AbstractDriver implements DriverInterface
{
    /**
     * Instance du pilote de génération de PDF.
     * @var mixed
     */
    protected $generator;

    /**
     * Instance du controleur HTTP associé.
     * @var callable
     */
    protected $renderer;

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
    public function setConfig(array $config): DriverInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRenderer(callable $renderer): DriverInterface
    {
        $this->renderer = $renderer;

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