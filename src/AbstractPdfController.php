<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Http\ResponseInterface;
use Pollen\Http\StreamedResponse;
use Pollen\Http\StreamedResponseInterface;
use Pollen\Pdf\Drivers\DompdfDriver;
use Pollen\Routing\BaseController;
use Pollen\Support\Str;

abstract class AbstractPdfController extends BaseController implements PdfControllerInterface
{
    /**
     * Instance du pilote de génération de PDF.
     * @var PdfDriverInterface
     */
    protected $driver;

    /**
     * Options de configuration du pilote de génération de PDF.
     * @var array
     */
    protected $driverOptions = [];

    /**
     * Nom de qualification du fichier.
     * @var string
     */
    protected $filename = 'file';

    /**
     * Méthode de rappel du traitement de la requête.
     * @var callable
     */
    protected $handler;

    /**
     * Méthode de rappel du rendu HTML du PDF.
     * @var callable
     */
    protected $htmlRenderer;

    /**
     * Indicateur de stockage du fichier PDF.
     * @var string //FileSystem
     */
    protected $storageDisk = false;

    /**
     * Indicateur de stockage du fichier PDF.
     * @var bool
     */
    protected $stored = false;

    /**
     * Indicateur de stockage du fichier PDF.
     * @var bool
     */
    protected $rewriteStorage = false;

    /**
     * @inheritDoc
     */
    public function defaultDriverOptions(): array
    {
        return [
            'base_path'   => $this->httpRequest()->getDocumentRoot(),
            'orientation' => 'portrait',
            'size'        => 'A4',
            'options'     => [
                'isPhpEnabled'         => true,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ],
        ];
    }

    /**
     * Traitement de la requête HTTP.
     *
     * @param array ...$args
     *
     * @return void
     */
    protected function handle(...$args): void
    {
        if (is_callable(($handler = $this->handler))) {
            $handler($this, ...$args);
        }
    }

    /**
     * @inheritDoc
     */
    public function driver(): PdfDriverInterface
    {
        if ($this->driver === null) {
            $this->driver = $this->containerHas(PdfDriverInterface::class)
                ? $this->containerGet(PdfDriverInterface::class) : new DompdfDriver();
        }

        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @inheritDoc
     */
    public function getHtmlRenderer(): string
    {
        return is_callable($html = $this->htmlRenderer) ? $html($this) : $this->htmlOutput();
    }

    /**
     * @inheritDoc
     */
    public function getDriverOptions(): array
    {
        return array_merge($this->driverOptions, $this->defaultDriverOptions());
    }

    /**
     * @inheritDoc
     */
    public function htmlOutput(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function responseDefault($disposition = 'inline'): StreamedResponseInterface
    {
        $response = new StreamedResponse();
        $disposition = $response->headers->makeDisposition($disposition, Str::ascii($this->getFilename()) . '.pdf');
        $response->headers->replace(
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => $disposition,
            ]
        );

        $response->setCallback(
            function () {
                $this->driver()->setRenderer([$this, 'getHtmlRenderer']);
                $this->driver()->setConfig($this->getDriverOptions());

                $stream = /*$this->storage() ? $this->store() :*/ $this->driver()->stream();
                fpassthru($stream);
                fclose($stream);
            }
        );

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function responseDisplay(...$args): StreamedResponseInterface
    {
        $this->handle(...$args);

        return $this->responseDefault();
    }

    /**
     * @inheritDoc
     */
    public function responseDownload(...$args): StreamedResponseInterface
    {
        $this->handle(...$args);

        return $this->responseDefault('attachment');
    }

    /**
     * @inheritDoc
     */
    public function responseHtml(...$args): ResponseInterface
    {
        $this->handle(...$args);

        return $this->response($this->getHtmlRenderer());
    }

    /**
     *
     * /
     * public function storage(): ?LocalFilesystem
     * {
     * if (is_null($this->storage)) {
     * $storage = $this->get('storage');
     *
     * if (is_string($storage)) {
     * $manager = $this->getContainer() ? $this->getContainer()->get('storage') : new StorageManager();
     * $storage = $manager->localFilesytem($storage);
     * }
     *
     * $this->storage = ($storage instanceof LocalFilesystem) ? $storage : null;
     * }
     *
     * return $this->storage;
     * }
     * /**/

    /**
     *
     * /
     * public function store()
     * {
     * if ($storage = $this->storage()) {
     * if (!$storage->has($this->getFilename()) || $this->renew()) {
     * $storage->putStream($this->getFilename(), $this->adapter()->stream());
     * }
     *
     * return $this->storage()->readStream($this->getFilename());
     * }
     *
     * return null;
     * }
     * /**/

    /**
     * @inheritDoc
     */
    public function setDriver(PdfDriverInterface $driver): PdfControllerInterface
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDriverOptions(array $driverOptions): PdfControllerInterface
    {
        $this->driverOptions = $driverOptions;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFilename(string $filename): PdfControllerInterface
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStorageDisk(string /** FileSystem */ $storageDisk): PdfControllerInterface
    {
        $this->storageDisk = $storageDisk;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setStored(bool $stored = true): PdfControllerInterface
    {
        $this->stored = $stored;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRewriteStorage(bool $rewriteStorage = true): PdfControllerInterface
    {
        $this->rewriteStorage = $rewriteStorage;

        return $this;
    }
}