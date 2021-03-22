<?php

declare(strict_types=1);

namespace Pollen\Pdf;

use Pollen\Http\ResponseInterface;
use Pollen\Http\StreamedResponseInterface;
use Pollen\Routing\BaseController;

/**
 * @mixin BaseController
 */
interface PdfControllerInterface
{
    /**
     * Liste des options par défaut du pilote de génération de PDF.
     */
    public function defaultDriverOptions(): array;

    /**
     * Récupération de l'instance du pilote de génération de PDF.
     *
     * @return PdfDriverInterface
     */
    public function driver(): PdfDriverInterface;

    /**
     * Récupération du nom de qualification du fichier PDF.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Récupération du rendu HTML du PDF.
     *
     * @return string
     */
    public function getHtmlRenderer(): string;

    /**
     * Récupération des options de configuration du pilote de génération de PDF.
     *
     * @return array
     */
    public function getDriverOptions(): array;

    /**
     * Récupération de la sortie HTML du PDF.
     *
     * @return string
     */
    public function htmlOutput(): string;

    /**
     * Récupération de la réponse HTTP par défaut.
     *
     * @param string $disposition inline|attachment.
     *
     * @return StreamedResponseInterface
     */
    public function responseDefault($disposition = 'inline'): StreamedResponseInterface;

    /**
     * Affichage du PDF.
     *
     * @param array ...$args Liste des variable passées en argument à la requête HTTP.
     *
     * @return mixed
     */
    public function responseDisplay(...$args): StreamedResponseInterface;

    /**
     * Téléchargement du PDF.
     *
     * @param array ...$args Liste des variable passées en argument à la requête HTTP.
     *
     * @return StreamedResponseInterface
     */
    public function responseDownload(...$args): StreamedResponseInterface;

    /**
     * Affichage du HTML
     *
     * @param array ...$args Liste des variable passées en argument à la requête HTTP.
     *
     * @return ResponseInterface
     */
    public function responseHtml(...$args): ResponseInterface;

    /**
     * Récupération de l'instance du gestionnaire de stockage du fichier.
     *
     * @return null
     * /
    public function storage(): ?LocalFilesystem;
    /**/

    /**
     * Stockage du fichier dans le répertoire de dépôt.
     *
     * @return mixed
     * /
    public function store();
    /**/

    /**
     * Définition du pilote de génération de PDF.
     *
     * @param PdfDriverInterface $driver
     *
     * @return static
     */
    public function setDriver(PdfDriverInterface $driver): PdfControllerInterface;

    /**
     * Définition des options de configuration du pilote de génération de PDF.
     *
     * @param array $driverOptions
     *
     * @return static
     */
    public function setDriverOptions(array $driverOptions): PdfControllerInterface;

    /**
     * Définition du nom de qualification du fichier PDF.
     *
     * @param string $filename
     *
     * @return static
     */
    public function setFilename(string $filename): PdfControllerInterface;

    /**
     * Définition du disque de stockage du fichier PDF généré.
     *
     * @param string $storageDisk // |FileSystem
     *
     * @return static
     */
    public function setStorageDisk(string /** FileSystem */$storageDisk): PdfControllerInterface;

    /**
     * Activation de l'enregistrement du fichier PDF généré dans le disque de stockage.
     *
     * @param bool $stored
     *
     * @return static
     */
    public function setStored(bool $stored = true): PdfControllerInterface;

    /**
     * Activation de la réécriture du fichier PDF généré dans le disque de stockage.
     *
     * @param bool $rewriteStorage
     *
     * @return static
     */
    public function setRewriteStorage(bool $rewriteStorage = true): PdfControllerInterface;
}