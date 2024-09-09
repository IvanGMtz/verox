<?php

namespace AppBundle\Entity;

/**
 * Proceso
 */
class Proceso
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $cantidad;

    /**
     * @var int
     */
    private $orden;

    /**
     * @var string
     */
    private $proceso;
    public function __toString() {
        return $this->proceso;
    }
    /**
     * @var string
     */
    private $tipoOrden;

    /**
     * @var \DateTime|null
     */
    private $fechaInicio;

    /**
     * @var int
     */
    private $status;

    /**
     * @var \DateTime|null
     */
    private $fechaFinalizacion;

    /**
     * @var int|null
     */
    private $duracion;

    /**
     * @var \AppBundle\Entity\Diseno
     */
    private $diseno;

    /**
     * @var \AppBundle\Entity\FosUser
     */
    private $userCreacion;


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
     * Set cantidad.
     *
     * @param int $cantidad
     *
     * @return Proceso
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set orden.
     *
     * @param int $orden
     *
     * @return Proceso
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden.
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set proceso.
     *
     * @param string $proceso
     *
     * @return Proceso
     */
    public function setProceso($proceso)
    {
        $this->proceso = $proceso;

        return $this;
    }

    /**
     * Get proceso.
     *
     * @return string
     */
    public function getProceso()
    {
        return $this->proceso;
    }

    /**
     * Set tipoOrden.
     *
     * @param string $tipoOrden
     *
     * @return Proceso
     */
    public function setTipoOrden($tipoOrden)
    {
        $this->tipoOrden = $tipoOrden;

        return $this;
    }

    /**
     * Get tipoOrden.
     *
     * @return string
     */
    public function getTipoOrden()
    {
        return $this->tipoOrden;
    }

    /**
     * Set fechaInicio.
     *
     * @param \DateTime|null $fechaInicio
     *
     * @return Proceso
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio.
     *
     * @return \DateTime|null
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Proceso
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set fechaFinalizacion.
     *
     * @param \DateTime|null $fechaFinalizacion
     *
     * @return Proceso
     */
    public function setFechaFinalizacion($fechaFinalizacion = null)
    {
        $this->fechaFinalizacion = $fechaFinalizacion;

        return $this;
    }

    /**
     * Get fechaFinalizacion.
     *
     * @return \DateTime|null
     */
    public function getFechaFinalizacion()
    {
        return $this->fechaFinalizacion;
    }

    /**
     * Set duracion.
     *
     * @param int|null $duracion
     *
     * @return Proceso
     */
    public function setDuracion($duracion = null)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Get duracion.
     *
     * @return int|null
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Set diseno.
     *
     * @param \AppBundle\Entity\Diseno|null $diseno
     *
     * @return Proceso
     */
    public function setDiseno(\AppBundle\Entity\Diseno $diseno = null)
    {
        $this->diseno = $diseno;

        return $this;
    }

    /**
     * Get diseno.
     *
     * @return \AppBundle\Entity\Diseno|null
     */
    public function getDiseno()
    {
        return $this->diseno;
    }

    /**
     * Set userCreacion.
     *
     * @param \AppBundle\Entity\FosUser|null $userCreacion
     *
     * @return Proceso
     */
    public function setUserCreacion(\AppBundle\Entity\FosUser $userCreacion = null)
    {
        $this->userCreacion = $userCreacion;

        return $this;
    }

    /**
     * Get userCreacion.
     *
     * @return \AppBundle\Entity\FosUser|null
     */
    public function getUserCreacion()
    {
        return $this->userCreacion;
    }
}
