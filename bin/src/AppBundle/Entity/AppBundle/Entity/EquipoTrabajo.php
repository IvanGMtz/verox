<?php

namespace AppBundle\Entity;

/**
 * EquipoTrabajo
 */
class EquipoTrabajo
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $area;

    /**
     * @var bool
     */
    private $estado;

    /**
     * @var int
     */
    private $cc;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return EquipoTrabajo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set area.
     *
     * @param string $area
     *
     * @return EquipoTrabajo
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area.
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set estado.
     *
     * @param bool $estado
     *
     * @return EquipoTrabajo
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return bool
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set cc.
     *
     * @param int $cc
     *
     * @return EquipoTrabajo
     */
    public function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * Get cc.
     *
     * @return int
     */
    public function getCc()
    {
        return $this->cc;
    }
}
