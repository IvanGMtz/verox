<?php

namespace AppBundle\Entity;

/**
 * ProcesoEncargado
 */
class ProcesoEncargado
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
     * @var bool
     */
    private $enProceso;

    /**
     * @var \DateTime
     */
    private $fechaAsignacion;

    /**
     * @var \AppBundle\Entity\Diseno
     */
    private $diseno;

    /**
     * @var \AppBundle\Entity\EquipoTrabajo
     */
    private $personaAsignada;

    /**
     * @var \AppBundle\Entity\Proceso
     */
    private $proceso;


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
     * @return ProcesoEncargado
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
     * Set enProceso.
     *
     * @param bool $enProceso
     *
     * @return ProcesoEncargado
     */
    public function setEnProceso($enProceso)
    {
        $this->enProceso = $enProceso;

        return $this;
    }

    /**
     * Get enProceso.
     *
     * @return bool
     */
    public function getEnProceso()
    {
        return $this->enProceso;
    }

    /**
     * Set fechaAsignacion.
     *
     * @param \DateTime $fechaAsignacion
     *
     * @return ProcesoEncargado
     */
    public function setFechaAsignacion($fechaAsignacion)
    {
        $this->fechaAsignacion = $fechaAsignacion;

        return $this;
    }

    /**
     * Get fechaAsignacion.
     *
     * @return \DateTime
     */
    public function getFechaAsignacion()
    {
        return $this->fechaAsignacion;
    }

    /**
     * Set diseno.
     *
     * @param \AppBundle\Entity\Diseno|null $diseno
     *
     * @return ProcesoEncargado
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
     * Set personaAsignada.
     *
     * @param \AppBundle\Entity\EquipoTrabajo|null $personaAsignada
     *
     * @return ProcesoEncargado
     */
    public function setPersonaAsignada(\AppBundle\Entity\EquipoTrabajo $personaAsignada = null)
    {
        $this->personaAsignada = $personaAsignada;

        return $this;
    }

    /**
     * Get personaAsignada.
     *
     * @return \AppBundle\Entity\EquipoTrabajo|null
     */
    public function getPersonaAsignada()
    {
        return $this->personaAsignada;
    }

    /**
     * Set proceso.
     *
     * @param \AppBundle\Entity\Proceso|null $proceso
     *
     * @return ProcesoEncargado
     */
    public function setProceso(\AppBundle\Entity\Proceso $proceso = null)
    {
        $this->proceso = $proceso;

        return $this;
    }

    /**
     * Get proceso.
     *
     * @return \AppBundle\Entity\Proceso|null
     */
    public function getProceso()
    {
        return $this->proceso;
    }
}
