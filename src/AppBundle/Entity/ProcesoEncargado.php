<?php

namespace AppBundle\Entity;

/**
 * Proceso
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
     * @var string
     */
    private $talla;

    /**
     * @var int
     */
    private $cantidadMaterial;

    /**
     * @var string
     */
    private $material;

    /**
     * @var bool
     */
    private $enProceso;

    /**
     * @var \DateTime
     */
    private $fechaAsignacion;

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
     * Set cantidadMaterial.
     *
     * @param int $cantidadMaterial
     *
     * @return ProcesoEncargado
     */
    public function setCantidadMaterial($cantidadMaterial)
    {
        $this->cantidadMaterial = $cantidadMaterial;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return int
     */
    public function getCantidadMaterial()
    {
        return $this->cantidadMaterial;
    }

    /**
     * Set material.
     *
     * @param string $material
     *
     * @return ProcesoEncargado
     */
    public function setMaterial($material)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material.
     *
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set talla.
     *
     * @param string $talla
     *
     * @return ProcesoEncargado
     */
    public function setTalla($talla)
    {
        $this->talla = $talla;

        return $this;
    }

    /**
     * Get talla.
     *
     * @return string
     */
    public function getTalla()
    {
        return $this->talla;
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
     * Set fechaFinalizacion.
     *
     * @param \DateTime|null $fechaFinalizacion
     *
     * @return ProcesoEncargado
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
     * @return ProcesoEncargado
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
