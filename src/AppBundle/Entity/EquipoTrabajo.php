<?php

namespace AppBundle\Entity;

/**
 * Proceso
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
    public function __toString() {
        return $this->nombre;
    }

    /**
     * @var string
     */
    private $area;

    /**
     * @var string
     */
    private $direccion;

    /**
     * @var string
     */
    private $telefono;

    /**
     * @var bool
     */
    private $activo;

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
     * Set direccion.
     *
     * @param string $direccion
     *
     * @return EquipoTrabajo
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set telefono.
     *
     * @param string $telefono
     *
     * @return EquipoTrabajo
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono.
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set activo.
     *
     * @param bool $activo
     *
     * @return EquipoTrabajo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo.
     *
     * @return bool
     */
    public function getActivo()
    {
        return $this->activo;
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
