<?php

declare(strict_types=1);

namespace Pollen\Pdf\Drivers;

use Pollen\Pdf\Controller\PdfControllerInterface;

interface DriverInterface
{
    /**
     * Résolution de la classe sous la forme d'un chaine de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Génération du PDF.
     *
     * @return static
     */
    public function generate(): DriverInterface;

    /**
     * Récupération du pilote génération de PDF.
     *
     * @return object
     */
    public function generator(): object;

    /**
     * Récupération de la sortie d'affichage du PDF.
     *
     * @return string
     */
    public function output(): string;

    /**
     * Définition de la liste des options
     *
     * @param array $params Liste des paramètres de configuration.
     *
     * @return static
     */
    public function setConfig(array $params): DriverInterface;

    /**
     * Définition du controleur associé.
     *
     * @param PdfControllerInterface $controller
     *
     * @return DriverInterface
     */
    public function setController(PdfControllerInterface $controller): DriverInterface;

    /**
     * Récupération de la sortie stream du PDF.
     *
     * @return resource|mixed|null
     */
    public function stream();
}