<?php

declare(strict_types=1);

namespace Pollen\Pdf\Drivers;

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
     * Définition des options de configuration
     *
     * @param array $config
     *
     * @return static
     */
    public function setConfig(array $config): DriverInterface;

    /**
     * Définition du controleur associé.
     *
     * @param callable $renderer
     *
     * @return DriverInterface
     */
    public function setRenderer(callable $renderer): DriverInterface;

    /**
     * Récupération de la sortie stream du PDF.
     *
     * @return resource|mixed|null
     */
    public function stream();
}