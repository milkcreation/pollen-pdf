<?php

declare(strict_types=1);

namespace Pollen\Pdf\Controller;

use Pollen\Http\ResponseInterface;
use Pollen\Http\StreamedResponse;
use Pollen\Http\StreamedResponseInterface;
use Pollen\Pdf\Drivers\DriverInterface;
use Pollen\Routing\BaseController;
use Pollen\Support\Filesystem as fs;

class BasePdfController extends BaseController implements PdfControllerInterface
{
    /**
     * Instance du pilote de génération de PDF.
     * @var DriverInterface
     */
    protected $driver;

    /**
     * Chemin racine.
     * @var string|null
     */
    private $base;

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'filename' => 'file.pdf',
            'handle'   => function (PdfControllerInterface $controller, ...$args) {
                return null;
            },
            'html'     => function (PdfControllerInterface $controller) {
                return '';
            },
            'pdf'      => [
                'driver'      => 'dompdf',
                'base_path'   => ROOT_PATH,
                'orientation' => 'portrait',
                'size'        => 'A4',
                'options'     => [
                    'isPhpEnabled'         => true,
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => true,
                ],
            ],
            'renew'    => false,
            'storage'  => false,
        ];
    }

    /**
     * @inheritDoc
     */
    public function dispatch(...$args): PdfControllerInterface
    {
        $this->parse();

        if (is_callable(($handler = $this->params('handle')))) {
            $handler($this, ...$args);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function driver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): string
    {
        return $this->params('filename');
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return is_callable($html = $this->params('html', '')) ? $html($this) : $html;
    }

    /**
     * @inheritDoc
     */
    public function parse(): PdfControllerInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function path(string $rel): string
    {
        return fs::normalizePath($this->base . '/' . $rel);
    }

    /**
     * @inheritDoc
     */
    public function renew(): bool
    {
        return (bool)$this->params('renew', false);
    }

    /**
     * @inheritDoc
     */
    public function responseDefault($disposition = 'inline'): StreamedResponseInterface
    {
        if (is_null($this->base)) {
            $this->setBase(ROOT_PATH);
        }

        $this->setDriver();

        $response = new StreamedResponse();
        $disposition = $response->headers->makeDisposition($disposition, $this->getFilename());
        $response->headers->replace([
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition,
        ]);

        $response->setCallback(function () {
            $stream = /*$this->storage() ? $this->store() :*/ $this->driver()->stream();
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function responseDisplay(...$args): StreamedResponseInterface
    {
        return $this->dispatch(...$args)->responseDefault();
    }

    /**
     * @inheritDoc
     */
    public function responseDownload(...$args): StreamedResponseInterface
    {
        return $this->dispatch(...$args)->responseDefault('attachment');
    }

    /**
     * @inheritDoc
     */
    public function responseHtml(...$args): ResponseInterface
    {
        /*if (is_null($this->base)) {
            $this->setBase(Url::scope());
        }*/

        return $this->response($this->dispatch(...$args)->html());
    }

    /**
     * @inheritDoc
     */
    public function setDriver(?DriverInterface $driver = null): PdfControllerInterface
    {
        if ($driver) {
            $this->driver = $driver;
        } elseif ($container = $this->getContainer()) {
            $alias = $this->params()->pull('pdf.driver', 'dompdf');
            $this->driver = $container->has("pdf.adapter.{$alias}")
                ? $container->get("pdf.adapter.{$alias}") : $container->get(DriverInterface::class);
        } else {
            switch ($this->params()->pull('pdf.driver', 'dompdf')) {
                default :
                case 'dompdf' :
                    $this->driver = new Dompdf();
                    break;
            }
        }

        $this->driver->setController($this)->setConfig($this->params()->pull('pdf', []));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBase(string $base): PdfControllerInterface
    {
        $this->base = $base;

        return $this;
    }

    /**
     *
     * /
    public function storage(): ?LocalFilesystem
    {
        if (is_null($this->storage)) {
            $storage = $this->get('storage');

            if (is_string($storage)) {
                $manager = $this->getContainer() ? $this->getContainer()->get('storage') : new StorageManager();
                $storage = $manager->localFilesytem($storage);
            }

            $this->storage = ($storage instanceof LocalFilesystem) ? $storage : null;
        }

        return $this->storage;
    }
    /**/

    /**
     *
     * /
    public function store()
    {
        if ($storage = $this->storage()) {
            if (!$storage->has($this->getFilename()) || $this->renew()) {
                $storage->putStream($this->getFilename(), $this->adapter()->stream());
            }

            return $this->storage()->readStream($this->getFilename());
        }

        return null;
    }
    /**/
}