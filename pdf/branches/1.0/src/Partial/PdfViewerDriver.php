<?php

declare(strict_types=1);

namespace Pollen\Pdf\Partial;

use Pollen\Partial\PartialDriver;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Pdf\PdfInterface;
use Pollen\Pdf\PdfProxy;

class PdfViewerDriver extends PartialDriver implements PdfViewerDriverInterface
{
    use PdfProxy;

    /**
     * @param PdfInterface $pdf
     * @param PartialManagerInterface $partialManager
     */
    public function __construct(PdfInterface $pdf, PartialManagerInterface $partialManager)
    {
        $this->setPdf($pdf);

        parent::__construct($partialManager);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'observe' => true,
                'defer'   => false,
                'content' => [
                    'footer' => false,
                    'header' => false,
                ],
                'nav'     => [
                    'first' => '&#171;',
                    'prev'  => '&#8249;',
                    'next'  => '&#8250;',
                    'last'  => '&#187;',
                ],
                'page'    => [
                    'current' => true,
                    'total'   => true,
                ],
                'spinner' => true,
                'src'     => 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/examples/learning/helloworld.pdf',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultClasses = [
            'content.wrapper' => 'PdfViewer-content',
            'content.header'  => 'PdfViewer-contentHeader',
            'content.body'    => 'PdfViewer-contentBody',
            'content.footer'  => 'PdfViewer-contentFooter',
            'canvas'          => 'PdfViewer-canvas',
            'nav.wrapper'     => 'PdfViewer-nav',
            'nav.first'       => 'PdfViewer-navFirst',
            'nav.prev'        => 'PdfViewer-navPrev',
            'nav.next'        => 'PdfViewer-navNext',
            'nav.last'        => 'PdfViewer-navLast',
            'page.wrapper'    => 'PdfViewer-page',
            'page.current'    => 'PdfViewer-pageCurrent',
            'page.total'      => 'PdfViewer-pageTotal',
            'spinner'         => 'PdfViewer-spinner',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        if ($this->get('observe')) {
            $this->set('attrs.data-observe', 'pdf-viewer');
        }

        $this->set(
            [
                'attrs.data-options' => [
                    'classes' => $this->get('classes'),
                    'content' => $this->get('content'),
                    'defer'   => (bool)$this->get('defer'),
                    'spinner' => (bool)$this->get('spinner'),
                    'nav'     => $this->get('nav'),
                    'src'     => $this->get('src'),
                ],
            ]
        );

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->pdf()->resources('/views/partial/pdf-viewer');
    }
}