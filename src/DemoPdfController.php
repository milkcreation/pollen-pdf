<?php

declare(strict_types=1);

namespace Pollen\Pdf;

final class DemoPdfController extends AbstractPdfController
{
    /**
     * @inheritDoc
     */
    public function htmlOutput(): string
    {
        return $this->render('demo', $this->params()->all());
    }

    /**
     * @inheritDoc
     */
    public function viewEngineDirectory(): string
    {
        return realpath(dirname(__DIR__) . '/resources/');
    }
}