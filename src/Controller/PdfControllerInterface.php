<?php

declare(strict_types=1);

namespace Pollen\Pdf\Controller;

use Pollen\Http\ResponseInterface;
use Pollen\Http\StreamedResponseInterface;
use Pollen\Routing\BaseController;
use Pollen\Pdf\Drivers\DriverInterface;

/**
 * @mixin BaseController
 */
interface PdfControllerInterface
{
    /**
     * Récupération de l'instance du générateur de PDF.
     *
     * @return DriverInterface
     */
    public function driver(): DriverInterface;

    /**
     * Récupération du nom de qualification du fichier PDF.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Traitement d'une liste de variable passées en argument.
     *
     * @param array ...$args
     *
     * @return static
     */
    public function dispatch(...$args): PdfControllerInterface;

    /**
     * Récupération de la sortie HTML du PDF.
     *
     * @return string
     */
    public function html(): string;

    /**
     * Récupération du chemin vers une ressource depuis un chemin relatif.
     *
     * @param string $rel
     *
     * @return string
     */
    public function path(string $rel): string;

    /**
     * Récupération de l'état de demande de renouvellement du fichier stocké.
     *
     * @return bool
     */
    public function renew(): bool;

    /**
     * Récupération de la reponse HTTP par défaut.
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
     * Définition de l'instance du pilote de génération de PDF.
     *
     * @param DriverInterface|null $driver
     *
     * @return static
     */
    public function setDriver(?DriverInterface $driver = null): PdfControllerInterface;

    /**
     * Définition du chemin racine.
     *
     * @param string $base
     *
     * @return static
     */
    public function setBase(string $base): PdfControllerInterface;

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
}